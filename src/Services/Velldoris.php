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
        array_unshift($uri_segments, $velldoris_app_config->frontHomeUrlSegment);
    
        $resource = $resourceModel
            ->select("{$resourceModel->table}.id")
            ->where("{$resourceModel->table}.url", $uri_segments[array_key_last($uri_segments)])
            ->first();
        
        // The current resource not found
        if (is_null($resource))
            return;
    
        $resource = $resourceModel->getResourceBranch($resource->id);
        $resource_segments = $resourceModel->getResourceBranchSegments($resource);
    
        // The current uri does not match any resource route
        if ($uri_segments !== array_reverse($resource_segments))
            return;
        
        $this->setResource($resource);
        $routes->add($uri_string, $resource->template_class_method);
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