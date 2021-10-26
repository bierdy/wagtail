<?php

namespace Wagtail\Controllers\Back;

class Templates extends BaseController
{
    public function list()
    {
        $templates = $this->wagtailModel->db
            ->table("{$this->templateModel->table} AS t")
            ->select("t.*, COUNT(DISTINCT r.id) AS resources_count")
            ->join("{$this->resourceModel->table} AS r", 't.id = r.template_id', 'left')
            ->groupBy('t.id')
            ->orderBy('t.id')
            ->get()
            ->getResult();
    
        $resources_count = array_reduce($templates, fn($count, $template) => $count + $template->resources_count, 0);
        
        $custom_data = [
            'title' => 'Templates',
            'templates' => $templates,
            'resources_count' => $resources_count,
        ];
        
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\templates\list', $data);
    }
    
    public function add()
    {
        $post = $this->request->getPost();
        
        if (! empty($post))
        {
            $post_variables = [];
            
            if (! empty($post['variables']))
            {
                foreach($post['variables'] as $post_variable_id => $post_variable)
                    if (! empty($post_variable['checked']))
                        $post_variables[] = [
                            'variable_id' => $post_variable_id,
                            'order' => $post_variable['order'],
                        ];
            }
    
            $errors = [];
    
            if ($this->templateModel->validate($post) === false)
                $errors = array_merge($errors, $this->templateModel->errors());
            
            foreach($post_variables as $post_variable)
                if ($this->templateVariableModel->validate($post_variable) === false)
                {
                    $errors['variables'][$post_variable['variable_id']] = $this->templateVariableModel->errors();
                    $this->templateVariableModel->validation->reset();
                }
                
            if (! empty($errors))
                $message = 'Validation errors:';
            else
            {
                $id = $this->templateModel->insert($post);
                foreach($post_variables as $post_variable)
                {
                    $post_variable['template_id'] = $id;
                    $this->templateVariableModel->insert($post_variable);
                }
    
                setWagtailCookie('message', 'The template was successfully created.');
                return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::edit', $id)));
            }
        }
        
        $variables = $this->variableModel->findAll();
        
        $custom_data =
            [
                'title' => 'Add template',
                'post' => $post,
                'variables_options' => ! empty($variables) ? array_combine(array_column($variables, 'id'), array_column($variables, 'title')) : [],
                'message' => $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\templates\add', $data);
    }
    
    public function edit(int $id = 0)
    {
        $post = $this->request->getPost();
        
        if (! empty($post))
        {
            $post_variables = [];
        
            if (! empty($post['variables']))
            {
                foreach($post['variables'] as $post_variable_id => $post_variable)
                    $post_variables[] = [
                        'template_id' => $id,
                        'variable_id' => $post_variable_id,
                        'id' => $post_variable['id'],
                        'order' => $post_variable['order'],
                        'checked' => ! empty($post_variable['checked']),
                    ];
            }
        
            $errors = [];
        
            if ($this->templateModel->validate($post) === false)
                $errors = array_merge($errors, $this->templateModel->errors());
        
            foreach($post_variables as $post_variable)
            {
                $post_variable_checked = $post_variable['checked'];
                unset($post_variable['id'], $post_variable['checked']);
                
                if ($post_variable_checked && $this->templateVariableModel->validate($post_variable) === false)
                {
                    $errors['variables'][$post_variable['variable_id']] = $this->templateVariableModel->errors();
                    $this->templateVariableModel->validation->reset();
                }
            }
        
            if (! empty($errors))
                $message = 'Validation errors:';
            else
            {
                $this->templateModel->update($id, $post);
                foreach($post_variables as $post_variable)
                {
                    $post_variable_id = $post_variable['id'];
                    $post_variable_checked = $post_variable['checked'];
                    unset($post_variable['id'], $post_variable['checked']);
                    
                    if ($post_variable_checked && $post_variable_id)
                        $this->templateVariableModel->update($post_variable_id, $post_variable);
                    elseif ($post_variable_checked)
                        $this->templateVariableModel->insert($post_variable);
                    elseif ($post_variable_id)
                        $this->templateVariableModel->delete($post_variable_id);
                }
                
                setWagtailCookie('message', 'The template was successfully updated.');
                return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::edit', $id)));
            }
        }
    
        $template = $this->templateModel->find($id);
        $variables = $this->variableModel->findAll();
        $template_variables = $this->templateVariableModel->where('template_id', $id)->findAll();
        
        $custom_data =
            [
                'title' => 'Edit template "' . $template->title . '"',
                'post' => $post,
                'template' => $template,
                'variables_options' => ! empty($variables) ? array_combine(array_column($variables, 'id'), array_column($variables, 'title')) : [],
                'template_variables_options' => ! empty($template_variables) ? array_combine(array_column($template_variables, 'variable_id'), $template_variables) : [],
                'message' => getWagtailCookie('message', true) ?? $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\templates\edit', $data);
    }
    
    public function activate($id)
    {
        $this->templateModel->update($id, ['active' => 1]);
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::list')));
    }
    
    public function deactivate($id)
    {
        $this->templateModel->update($id, ['active' => 0]);
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::list')));
    }
    
    public function delete($id)
    {
        $this->templateModel->delete($id);
        $this->templateVariableModel->where('template_id', $id)->delete();
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::list')));
    }
    
    public function deleteAll()
    {
        $templates = $this->templateModel->findAll();
        foreach($templates as $template)
            $this->templateModel->delete($template->id);
    
        $template_variables = $this->templateVariableModel->findAll();
        foreach($template_variables as $template_variable)
            $this->templateVariableModel->delete($template_variable->id);
    
        $db = $this->wagtailModel->db;
        $db->table($this->templateModel->table)->truncate();
        $db->table($this->templateVariableModel->table)->truncate();
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::list')));
    }
}