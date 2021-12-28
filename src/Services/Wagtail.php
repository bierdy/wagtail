<?php

namespace Wagtail\Services;

use CodeIgniter\Config\BaseService;

class Wagtail extends BaseService
{
    protected $resource = null;
    
    public function setResourceRoute()
    {
        // We don't need to define a front controller in CLI request
        if (is_cli())
            return;
        
        helper('wagtail');
        $routes = service('Routes');
        $resourceModel = model('Resource');
        $wagtail_app_config = config('WagtailApp');
        
        $uri_string = uri_string(true);
        $uri_string = ! empty($uri_string) ? $uri_string : '/';
        $uri_segments = explode('/', $uri_string);
        $uri_segments = array_diff($uri_segments, ['']);
        if ($wagtail_app_config->frontRootPath === '/')
            array_unshift($uri_segments, $wagtail_app_config->frontRootPath);
    
        // If front root path of wagtail app config is not match first uri segment stop work
        if (empty($uri_segments))
            return;
        
        $resource = $resourceModel->getResourceTreeByUriSegments($uri_segments);
    
        if (! is_null($resource) && ! empty($resource->template_class_method))
        {
            $this->setResource($resource);
            $routes->add($uri_string, $resource->template_class_method, ['hostname' => $wagtail_app_config->frontDomain]);
            
            return;
        }
        
        $resource_404 = $resourceModel->getReverseResourceTree((int) $wagtail_app_config->resource404Id);
        
        if (! is_null($resource_404) && ! empty($resource_404->template_class_method))
        {
            $this->setResource($resource_404);
            $routes->set404Override($resource_404->template_class_method);
            
            return;
        }
    }
    
    public function getResource() : ? object
    {
        return is_null($this->resource) ? null : clone $this->resource;
    }
    
    protected function setResource(object $resource = null)
    {
        $this->resource = $resource;
    }
    
    public function setValidationRules()
    {
        $config_validation_rule_sets = config('Validation')->ruleSets;
    
        $validation_class_rules_class = \Wagtail\Validation\Rules\ClassRules::class;
    
        if (! in_array($validation_class_rules_class, $config_validation_rule_sets))
            config('Validation')->ruleSets[] = $validation_class_rules_class;
    }
}