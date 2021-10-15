<?php

namespace Velldoris\Controllers\Back;

class Variables extends BaseController
{
    public function list()
    {
        $variables = $this->velldorisModel->db
            ->table("{$this->variableModel->table} AS v")
            ->select("v.*, COUNT(DISTINCT tv.id) AS templates_count, COUNT(DISTINCT vv.id) AS values_count, l.title AS language_title")
            ->join("{$this->templateVariableModel->table} AS tv", 'v.id = tv.variable_id', 'left')
            ->join("{$this->variableValueModel->table} AS vv", 'v.id = vv.variable_id', 'left')
            ->join("{$this->languageModel->table} AS l", 'v.language_id = l.id', 'left')
            ->groupBy('v.id')
            ->orderBy('v.id')
            ->get()
            ->getResult();
    
        $templates_count = array_reduce($variables, fn($count, $variable) => $count + $variable->templates_count, 0);
        $values_count = array_reduce($variables, fn($count, $variable) => $count + $variable->values_count, 0);
        
        $custom_data = [
            'title' => 'Variables',
            'variables' => $variables,
            'templates_count' => $templates_count,
            'values_count' => $values_count,
        ];
        
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Velldoris\Views\back\templates\variables\list', $data);
    }
    
    public function add()
    {
        $post = $this->request->getPost();
        
        if (! empty($post) && ($id = $this->variableModel->insert($post)) === false)
        {
            $message = 'Validation errors:';
            $errors = $this->variableModel->errors();
        }
        elseif (! empty($post))
        {
            setVelldorisCookie('message', 'The variable was successfully created.');
            return $this->response->redirect(route_to('Velldoris\Controllers\Back\Variables::edit', $id));
        }
        
        $languages = $this->languageModel->findAll();
        
        $custom_data =
            [
                'title' => 'Add variable',
                'post' => $post,
                'languages_options' => ! empty($languages) ? ['' => 'Empty'] + array_combine(array_column($languages, 'id'), array_column($languages, 'title')) : ['' => 'Languages not found'],
                'message' => $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Velldoris\Views\back\templates\variables\add', $data);
    }
    
    public function edit(int $id = 0)
    {
        $post = $this->request->getPost();
        
        if (! empty($post) && $this->variableModel->update($id, $post) === false)
        {
            $message = 'Validation errors:';
            $errors = $this->variableModel->errors();
        }
        elseif (! empty($post))
        {
            setVelldorisCookie('message', 'The variable was successfully updated.');
            return $this->response->redirect(route_to('Velldoris\Controllers\Back\Variables::edit', $id));
        }
    
        $variable = $this->variableModel->find($id);
        $languages = $this->languageModel->findAll();
        
        $custom_data =
            [
                'title' => 'Edit variable "' . $variable->title . '"',
                'post' => $post,
                'variable' => $variable,
                'languages_options' => ! empty($languages) ? ['' => 'Empty'] + array_combine(array_column($languages, 'id'), array_column($languages, 'title')) : ['' => 'Languages not found'],
                'message' => getVelldorisCookie('message', true) ?? $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Velldoris\Views\back\templates\variables\edit', $data);
    }
    
    public function activate($id)
    {
        $this->variableModel->update($id, ['active' => 1]);
        
        return $this->response->redirect(route_to('Velldoris\Controllers\Back\Variables::list'));
    }
    
    public function deactivate($id)
    {
        $this->variableModel->update($id, ['active' => 0]);
        
        return $this->response->redirect(route_to('Velldoris\Controllers\Back\Variables::list'));
    }
    
    public function delete($id)
    {
        $this->variableModel->delete($id);
        $this->templateVariableModel->where('variable_id', $id)->delete();
        $this->variableValueModel->where('variable_id', $id)->delete();
        
        return $this->response->redirect(route_to('Velldoris\Controllers\Back\Variables::list'));
    }
    
    public function deleteAll()
    {
        $variables = $this->variableModel->findAll();
        foreach($variables as $variable)
            $this->variableModel->delete($variable->id);
    
        $template_variables = $this->templateVariableModel->findAll();
        foreach($template_variables as $template_variable)
            $this->templateVariableModel->delete($template_variable->id);
        
        $variable_values = $this->variableValueModel->findAll();
        foreach($variable_values as $variable_value)
            $this->variableValueModel->delete($variable_value->id);
        
        $db = $this->velldorisModel->db;
        $db->table($this->variableModel->table)->truncate();
        $db->table($this->templateVariableModel->table)->truncate();
        $db->table($this->variableValueModel->table)->truncate();
        
        return $this->response->redirect(route_to('Velldoris\Controllers\Back\Variables::list'));
    }
    
    public function deleteValue(int $value_id = 0)
    {
        $variable_value = $this->variableValueModel->find($value_id);
        $resource = $this->resourceModel->find($variable_value->resource_id);
        $variable = $this->variableModel->find($variable_value->variable_id);
        $variable_handler = new $variable->class([], $resource, $variable);
        
        if (method_exists($variable_handler, 'deleteValue'))
            $variable_handler->deleteValue($value_id);
        else
        {
            $this->variableValueModel->delete($value_id);
            setVelldorisCookie('message', 'The value of "' . ucfirst(mb_strtolower($variable->title)) . '" variable was successfully deleted.');
        }
    
        return $this->response->redirect(previous_url());
    }
}