<?php

namespace Wagtail\Controllers\Back;

class Languages extends BaseController
{
    public function list()
    {
        $languages = $this->wagtailModel->db
            ->table("{$this->languageModel->table} AS l")
            ->select("l.*, COUNT(DISTINCT v.id) AS variables_count")
            ->join("{$this->variableModel->table} AS v", 'l.id = v.language_id', 'left')
            ->groupBy('l.id')
            ->orderBy('l.id')
            ->get()
            ->getResult();
    
        $variables_count = array_reduce($languages, fn($count, $language) => $count + $language->variables_count, 0);
        
        $custom_data = [
            'title' => 'Language',
            'languages' => $languages,
            'variables_count' => $variables_count,
        ];
        
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\languages\list', $data);
    }
    
    public function add()
    {
        $post = $this->request->getPost();
        
        if (! empty($post) && ($id = $this->languageModel->insert($post)) === false)
        {
            $message = 'Validation errors:';
            $errors = $this->languageModel->errors();
        }
        elseif (! empty($post))
        {
            setWagtailCookie('message', 'The language was successfully created.');
            return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Languages::edit', $id)));
        }
        
        $custom_data =
            [
                'title' => 'Add language',
                'post' => $post,
                'message' => $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\languages\add', $data);
    }
    
    public function edit(int $id = 0)
    {
        $post = $this->request->getPost();
        
        if (! empty($post) && $this->languageModel->update($id, $post) === false)
        {
            $message = 'Validation errors:';
            $errors = $this->languageModel->errors();
        }
        elseif (! empty($post))
        {
            setWagtailCookie('message', 'The language was successfully updated.');
            return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Languages::edit', $id)));
        }
    
        $language = $this->languageModel->find($id);
        
        $custom_data =
            [
                'title' => 'Edit language "' . $language->title . '"',
                'post' => $post,
                'language' => $language,
                'message' => getWagtailCookie('message', true) ?? $message ?? '',
                'errors' => $errors ?? [],
            ];
        $data = array_merge($this->default_data, $custom_data);
        
        echo view('Wagtail\Views\back\templates\languages\edit', $data);
    }
    
    public function activate($id)
    {
        $this->languageModel->update($id, ['active' => 1]);
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Languages::list')));
    }
    
    public function deactivate($id)
    {
        $this->languageModel->update($id, ['active' => 0]);
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Languages::list')));
    }
    
    public function delete($id)
    {
        $this->languageModel->delete($id);
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Languages::list')));
    }
    
    public function deleteAll()
    {
        $languages = $this->languageModel->findAll();
        foreach($languages as $language)
            $this->languageModel->delete($language->id);
        
        $this->wagtailModel->db->table($this->languageModel->table)->truncate();
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Languages::list')));
    }
    
    public function setDefault($id)
    {
        $this->languageModel->where('default', 1)->set(['default' => 0])->update();
        $this->languageModel->update($id, ['default' => 1]);
        
        return $this->response->redirect(base_url(route_to('Wagtail\Controllers\Back\Languages::list')));
    }
}