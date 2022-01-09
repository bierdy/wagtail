<?php

namespace Wagtail\Controllers\Back;

class Resources extends BaseController
{
    public function add(int $parent_id = 0)
    {
        $post = $this->request->getPost();
        
        if (! empty($post))
        {
            $post['order'] = count($this->resourceModel->where(['parent_id' => $parent_id])->findAll());
            
            if ($this->resourceModel->validate($post) === false)
            {
                $message = 'Validation errors:';
                $errors = $this->resourceModel->errors();
            }
            else
            {
                $id = $this->resourceModel->insert($post);
                $this->resourceModel->updateUrl($id);
    
                setWagtailCookie('message', 'The resource was successfully created.');
                return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Resources::edit', $id)));
            }
        }
    
        $parent = $this->resourceModel->find($parent_id);
        $templates = $this->templateModel->getAvailableTemplates();
        
        $custom_data =
            [
                'title' => 'Add resource',
                'post' => $post,
                'parent' => $parent,
                'templates_options' => ! empty($templates) ? array_combine(array_column($templates, 'id'), array_column($templates, 'title')) : ['' => 'There are no available templates'],
                'message' => $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\resources\add', $data);
    }
    
    public function edit(int $id = 0)
    {
        $post = $this->request->getPost();
        $resource = $this->resourceModel->find($id);
        $variables = $this->variableModel->getVariablesWithValues($resource->id, $resource->template_id);
        
        $template_variable_groups = $this->wagtailModel->db
            ->table("{$this->variableGroupModel->table} AS vg")
            ->select('vg.*, tvg.order AS order')
            ->join("{$this->templateVariableGroupModel->table} AS tvg", 'vg.id = tvg.variable_group_id', 'inner')
            ->where(['tvg.template_id' => $resource->template_id])
            ->orderBy('order', 'ASC')
            ->get()
            ->getResult();
    
        $variable_group_variables = ! empty($template_variable_groups) ? $this->variableGroupVariableModel
            ->whereIn('variable_group_id', array_column($template_variable_groups, 'id'))
            ->orderBy('order', 'ASC')
            ->findAll() : [];
        
        if (! empty($post))
        {
            if (! empty($variables))
                foreach($variables as $variable)
                    if (! empty($variable->validation_rules))
                        $this->variableModel->setVariableValidationRules(json_decode($variable->validation_rules, true));
            
            if ($this->resourceModel->validate($post) === false)
            {
                $message = 'Validation errors:';
                $errors = $this->resourceModel->errors();
            }
            else
            {
                $this->resourceModel->update($id, $post);
                $this->resourceModel->updateUrl($id);
    
                if (! empty($variables))
                    foreach($variables as $variable)
                        (new $variable->class($post, $resource, $variable))->init();
                
                setWagtailCookie('message', 'The resource was successfully updated.');
                return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Resources::edit', $id)));
            }
        }
        
        $parent = $this->resourceModel->find($resource->parent_id);
        $template = $this->templateModel->find($resource->template_id);
        
        $custom_data =
            [
                'title' => 'Edit ' . ($template->unique ? '"' . $template->title . '" page' : mb_strtolower($template->title) . ' "' . $resource->title . '"'),
                'post' => $post,
                'resource' => $resource,
                'parent' => $parent,
                'template' => $template,
                'variables' => $variables,
                'template_variable_groups' => $template_variable_groups,
                'variable_group_variables' => $variable_group_variables,
                'message' => getWagtailCookie('message', true) ?? $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\resources\edit', $data);
    }
    
    public function activate(int $id = 0)
    {
        $this->resourceModel->update($id, ['active' => 1]);
        
        return $this->response->redirect(previous_url());
    }
    
    public function deactivate(int $id = 0)
    {
        $this->resourceModel->update($id, ['active' => 0]);
        
        return $this->response->redirect(previous_url());
    }
    
    public function delete(int $id = 0)
    {
        $parent_resource = $this->resourceModel->find($id);
        $parent_resource_childs = $this->resourceModel->getResourceChilds($id);
    
        $resources = $parent_resource_childs;
        array_unshift($resources, $parent_resource);
    
        $resources_ids = array_column($resources, 'id');
        
        // Delete variable values
        $variable_values = $this->variableValueModel->whereIn('resource_id', $resources_ids)->findAll();
    
        foreach ($resources as $resource)
        {
            foreach ($variable_values as $variable_value)
            {
                if ($variable_value->resource_id !== $resource->id)
                    continue;
                
                $variable = $this->variableModel->find($variable_value->variable_id);
                $variable_handler = new $variable->class([], $resource, $variable);
        
                if (method_exists($variable_handler, 'deleteValue'))
                    $variable_handler->deleteValue($variable_value->id);
                else
                    $this->variableValueModel->delete($variable_value->id);
            }
        }
        // /Delete variable values
        
        // Delete cookie open branches
        $cookies_resources_tree = getWagtailCookie('resources_tree');
        $cookies_resources_tree = ! is_null($cookies_resources_tree) ? json_decode($cookies_resources_tree) : null;
        $cookies_resources_tree_open_branches = ! empty($cookies_resources_tree->open_branches) && is_array($cookies_resources_tree->open_branches) ? $cookies_resources_tree->open_branches : [];
        
        if (! empty($cookies_resources_tree_open_branches))
        {
            $cookies_resources_tree_open_branches = array_diff($cookies_resources_tree_open_branches, $resources_ids);
            $cookies_resources_tree->open_branches = array_values($cookies_resources_tree_open_branches);
            
            setWagtailCookie('resources_tree', json_encode($cookies_resources_tree));
        }
        // /Delete cookie open branches
        
        $this->resourceModel->whereIn('id', $resources_ids)->delete();
    
        deleteWagtailCookie('message');
    
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Home::index')));
    }
    
    public function setTemplate(int $id = 0, int $template_id = 0)
    {
        $resource = $this->resourceModel->find($id);
        $variable_values = $this->variableValueModel->where('resource_id', $resource->id)->findAll();
        
        foreach($variable_values as $variable_value)
        {
            $variable = $this->variableModel->find($variable_value->variable_id);
            $variable_handler = new $variable->class([], $resource, $variable);
    
            if (method_exists($variable_handler, 'deleteValue'))
                $variable_handler->deleteValue($variable_value->id);
            else
                $this->variableValueModel->delete($variable_value->id);
        }
        
        $this->resourceModel->update($id, ['template_id' => $template_id]);
        
        setWagtailCookie('message', 'Template was successfully changed.');
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Resources::edit', $id)));
    }
    
    public function setParent(int $id = 0, int $parent_id = 0)
    {
        $result = $this->resourceModel->update($id, ['parent_id' => $parent_id]);
        $this->resourceModel->updateUrl($id);
    
        return json_encode(['result' => $result]);
    }
    
    public function setOrder(int $id = 0, int $order = 0)
    {
        $result = $this->resourceModel->update($id, ['order' => $order]);
    
        return json_encode(['result' => $result]);
    }
}