<?php

namespace Wagtail\Controllers\Back;

class Home extends BaseController
{
    public function index() : string
    {
        $custom_data = [
            'title' => 'Wagtail',
        ];
        
        $data = array_merge($this->default_data, $custom_data);
        
        return view('Wagtail\Views\back\templates\home', $data);
    }
}