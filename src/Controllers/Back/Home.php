<?php

namespace Velldoris\Controllers\Back;

class Home extends BaseController
{
    public function index() : string
    {
        $custom_data = [
            'title' => 'Home',
        ];
        
        $data = array_merge($this->default_data, $custom_data);
        
        return view('Velldoris\Views\back\templates\home', $data);
    }
}