<?php

namespace Wagtail\Controllers\Back;

use CodeIgniter\Controller;

class Assets extends Controller
{
    public function css(string $file_name = '')
    {
        $this->response->setContentType('text/css');
        $this->response->setBody(view("Wagtail\\Views\\back\\assets\\css\\{$file_name}.css"));
        $this->response->send();
    }
    
    public function js(string $file_name = '')
    {
        $this->response->setContentType('text/javascript');
        $this->response->setBody(view("Wagtail\\Views\\back\\assets\\js\\{$file_name}.js"));
        $this->response->send();
    }
}