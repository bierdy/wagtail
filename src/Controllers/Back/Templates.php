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
    
        if (! empty($post) && ($id = $this->templateModel->insert($post)) === false)
        {
            $message = 'Validation errors:';
            $errors = $this->templateModel->errors();
        }
        elseif (! empty($post))
        {
            setWagtailCookie('message', 'The template was successfully created.');
            return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::edit', $id)));
        }
        
        $custom_data =
            [
                'title' => 'Add template',
                'post' => $post,
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
                        'template_variable_id' => $post_variable['template_variable_id'],
                        'order' => $post_variable['order'],
                        'checked' => ! empty($post_variable['checked']),
                        'variable_group_id' => $post_variable['variable_group_id'],
                        'variable_group_id_original' => ! empty($post_variable['variable_group_id_original']) ? $post_variable['variable_group_id_original'] : '',
                    ];
            }
    
            $post_template_variable_groups = [];
    
            if (! empty($post['template_variable_groups']))
            {
                foreach($post['template_variable_groups'] as $post_template_variable_group)
                    $post_template_variable_groups[] = [
                        'variable_group_id' => $post_template_variable_group['id'],
                        'order' => $post_template_variable_group['order'],
                    ];
            }
        
            $errors = [];
        
            if ($this->templateModel->validate($post) === false)
                $errors = array_merge($errors, $this->templateModel->errors());
        
            if (! empty($errors))
                $message = 'Validation errors:';
            else
            {
                $this->templateModel->update($id, $post);
                foreach($post_variables as $post_variable)
                {
                    $post_variable_checked = $post_variable['checked'];
                    $template_variable_id = $post_variable['template_variable_id'];
                    unset($post_variable['template_variable_id'], $post_variable['checked']);
                    
                    if ($post_variable_checked && $template_variable_id)
                        $this->templateVariableModel->update($template_variable_id, $post_variable);
                    elseif ($post_variable_checked)
                        $this->templateVariableModel->insert($post_variable);
                    elseif ($template_variable_id)
                        $this->templateVariableModel->delete($template_variable_id);
                    
                    $variable_group_variable = $this->variableGroupVariableModel->where(['variable_group_id' => $post_variable['variable_group_id_original'], 'variable_id' => $post_variable['variable_id']])->first();
                    
                    if ($post_variable_checked && $post_variable['variable_group_id'] && ! is_null($variable_group_variable))
                        $this->variableGroupVariableModel->update($variable_group_variable->id, $post_variable);
                    elseif ($post_variable_checked && $post_variable['variable_group_id'])
                        $this->variableGroupVariableModel->insert($post_variable);
                    elseif (! is_null($variable_group_variable))
                        $this->variableGroupVariableModel->delete($variable_group_variable->id);
                }
                
                foreach($post_template_variable_groups as $post_template_variable_group)
                    $this->templateVariableGroupModel->where(['variable_group_id' => $post_template_variable_group['variable_group_id']])->set(['order' => $post_template_variable_group['order']])->update();
                
                setWagtailCookie('message', 'The template was successfully updated.');
                return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::edit', $id)));
            }
        }
        
        $template = $this->templateModel->find($id);
        $variables = $this->variableModel->orderBy('title')->findAll();
        $template_variables = $this->templateVariableModel->where('template_id', $id)->findAll();
        
        $template_variable_groups = $this->wagtailModel->db
            ->table("{$this->variableGroupModel->table} AS vg")
            ->select('vg.*, tvg.order AS order')
            ->join("{$this->templateVariableGroupModel->table} AS tvg", 'vg.id = tvg.variable_group_id', 'inner')
            ->where(['tvg.template_id' => $id])
            ->orderBy('order', 'ASC')
            ->get()
            ->getResult();
        
        $variable_group_variables = ! empty($template_variable_groups) && ! empty($template_variables) ? $this->variableGroupVariableModel
            ->whereIn('variable_group_id', array_column($template_variable_groups, 'id'))
            ->whereIn('variable_id', array_column($template_variables, 'variable_id'))
            ->orderBy('order', 'ASC')
            ->findAll() : [];
        
        $custom_data =
            [
                'title' => 'Edit template "' . $template->title . '"',
                'post' => $post,
                'template' => $template,
                'variables' => ! empty($variables) ? array_combine(array_column($variables, 'id'), $variables) : [],
                'template_variables' => ! empty($template_variables) ? array_combine(array_column($template_variables, 'variable_id'), $template_variables) : [],
                'template_variable_groups' => $template_variable_groups,
                'variable_group_variables' => $variable_group_variables,
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