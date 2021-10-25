<?php

namespace Velldoris\Services;

use CodeIgniter\Config\BaseService;

class Velldoris extends BaseService
{
    protected $resource = null;
    
    public function setResourceRoute()
    {
        // We don't need to define a front controller in CLI request
        if (is_cli())
            return;
        
        helper('velldoris');
        $routes = service('Routes');
        $resourceModel = model('Resource');
        $velldoris_app_config = config('VelldorisApp');
        
        $uri_string = uri_string(true);
        $uri_string = ! empty($uri_string) ? $uri_string : '/';
        $uri_segments = explode('/', $uri_string);
        $uri_segments = array_diff($uri_segments, ['']);
        if ($velldoris_app_config->frontRootPath === '/')
            array_unshift($uri_segments, $velldoris_app_config->frontRootPath);
    
        // If front root path of velldoris app config is not match first uri segment stop work
        if (empty($uri_segments))
            return;
    
        $resource = $resourceModel->getResourceTreeByUriSegments($uri_segments);
        
        // The current resource not found
        if (is_null($resource))
            return;
        
        $this->setResource($resource);
        $routes->add($uri_string, $resource->template_class_method, ['hostname' => $velldoris_app_config->frontDomain]);
    }
    
    public function getResource() : ? object
    {
        return $this->resource;
    }
    
    protected function setResource(object $resource = null)
    {
        $this->resource = $resource;
    }
}