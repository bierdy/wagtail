<?php

namespace Wagtail\Controllers\Back;

use CodeIgniter\Controller;

class Assets extends Controller
{
    public function get()
    {
        $file_path = $this->request->getGet('path');
        $file_name = $this->request->getGet('name');
        $file_ext = $this->request->getGet('ext');
        
        if (is_null($file_path) || is_null($file_name) || is_null($file_ext))
            return;
        
        if (! method_exists($this, $file_ext))
            return;
        
        $this->{$file_ext}();
        $this->response->setBody(view($file_path . $file_name . '.' . $file_ext, [], ['debug' => false]));
        $this->response->send();
    }
    
    protected function css()
    {
        $this->response->setContentType('text/css');
    }
    
    protected function csv()
    {
        $this->response->setContentType('text/csv');
    }
    
    protected function html()
    {
        $this->response->setContentType('text/html');
    }
    
    protected function js()
    {
        $this->response->setContentType('text/javascript');
    }
    
    protected function txt()
    {
        $this->response->setContentType('text/plain');
    }
    
    protected function xml()
    {
        $this->response->setContentType('text/xml');
    }
    
    protected function jpg()
    {
        $this->response->setContentType('image/jpeg');
    }
    
    protected function jpeg()
    {
        $this->response->setContentType('image/jpeg');
    }
    
    protected function png()
    {
        $this->response->setContentType('image/png');
    }
    
    protected function gif()
    {
        $this->response->setContentType('image/gif');
    }
    
    protected function webp()
    {
        $this->response->setContentType('image/webp');
    }
    
    protected function json()
    {
        $this->response->setContentType('application/json');
    }
    
    protected function pdf()
    {
        $this->response->setContentType('application/pdf');
    }
}