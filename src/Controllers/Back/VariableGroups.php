<?php

namespace Wagtail\Controllers\Back;

class VariableGroups extends BaseController
{
    public function add(int $template_id = 0)
    {
        $post = $this->request->getPost();
        
        if (! empty($post))
        {
            if (($id = $this->variableGroupModel->insert($post)) === false)
            {
                $message = 'Validation errors:';
                $errors = $this->variableGroupModel->errors();
            }
            elseif ($this->templateVariableGroupModel->insert(array_merge($post, ['variable_group_id' => $id])) === false)
            {
                $message = 'Validation errors:';
                $errors = $this->templateVariableGroupModel->errors();
    
                $this->variableGroupModel->delete($id);
            }
            else
            {
                setWagtailCookie('message', 'The variable group was successfully created.');
                return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::edit', $template_id)));
            }
            
        }
    
        $template_variable_groups_count = count($this->templateVariableGroupModel->where(['template_id' => $template_id])->find());
        
        $custom_data =
            [
                'title' => 'Add variable group',
                'post' => $post,
                'template_id' => $template_id,
                'template_variable_groups_count' => $template_variable_groups_count,
                'message' => $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\variable_groups\add', $data);
    }
    
    public function edit(int $id = 0)
    {
        $post = $this->request->getPost();
    
        if (! empty($post) && $this->variableGroupModel->update($id, $post) === false)
        {
            $message = 'Validation errors:';
            $errors = $this->variableGroupModel->errors();
        }
        elseif (! empty($post))
        {
            setWagtailCookie('message', 'The variable group was successfully updated.');
            return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\VariableGroups::edit', $id)));
        }
    
        $variable_group = $this->variableGroupModel->find($id);
        $template_variable_group = $this->templateVariableGroupModel->where(['variable_group_id' => $id])->first();
    
        $custom_data =
            [
                'title' => 'Edit variable group "' . $variable_group->title . '"',
                'post' => $post,
                'variable_group' => $variable_group,
                'template_variable_group' => $template_variable_group,
                'message' => getWagtailCookie('message', true) ?? $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
    
        echo view('Wagtail\Views\back\templates\variable_groups\edit', $data);
    }
    
    public function delete($id)
    {
        $variable_group = $this->variableGroupModel->find($id);
        
        if (! is_null($variable_group))
            $this->variableGroupModel->delete($id);
        
        $this->variableGroupVariableModel->where(['variable_group_id' => $id])->delete();
        
        $template_variable_group = $this->templateVariableGroupModel->where(['variable_group_id' => $id])->first();
        
        if (! is_null($template_variable_group))
        {
            $this->templateVariableGroupModel->delete($template_variable_group->id);
    
            $template_variable_groups_ = $this->templateVariableGroupModel->where(['template_id' => $template_variable_group->template_id])->orderBy('order', 'ASC')->findAll();
            
            foreach($template_variable_groups_ as $key_ => $template_variable_group_)
            {
                $template_variable_group_->order = $key_;
                
                $this->templateVariableGroupModel->update($template_variable_group_->id, $template_variable_group_);
            }
    
            setWagtailCookie('message', 'The variable group was successfully deleted.');
            return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Templates::edit', $template_variable_group->template_id)));
        }
    }
}