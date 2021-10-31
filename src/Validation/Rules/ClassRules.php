<?php

namespace Wagtail\Validation\Rules;

class ClassRules
{
    /**
     * Validate the existence of the class
     *
     * @param string $class Class or Class::method
     * @param string|null $error
     *
     * @return bool
     */
    public function is_class_exist(string $class, string &$error = null) : bool
    {
        if (empty($class))
            return true;
        
        $class = explode('::', $class)[0];
        
        if (! class_exists($class))
        {
            $error = "The class \"{$class}\" is not declared.";
        
            return false;
        }
    
        return true;
    }
    
    /**
     * Validate object interface implementation
     *
     * @param string $class Class or Class::method
     * @param string $interface
     * @param array $data
     * @param string|null $error
     *
     * @return bool
     */
    public function class_is_not_implement_interface(string $class, string $interface, array $data = [], string &$error = null) : bool
    {
        if (empty($class))
            return true;
    
        if (empty($interface))
        {
            $error = "Interface not specified.";
    
            return false;
        }
        
        $class = explode('::', $class)[0];
        $class_interfaces = class_implements($class);
        
        if (! isset($class_interfaces[$interface]))
        {
            $error = "The class \"{$class}\" must implement the interface \"{$interface}\".";
    
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate the existence of the class method
     *
     * @param string $class_method Class::method
     * @param string|null $error
     *
     * @return bool
     */
    public function is_method_exist(string $class_method, string &$error = null) : bool
    {
        if (empty($class_method))
            return true;
        
        $class = explode('::', $class_method)[0] ?? null;
        $method = explode('::', $class_method)[1] ?? null;
        
        if (is_null($method))
        {
            $error = "Method not specified.";
    
            return false;
        }
        
        if (! class_exists($class))
        {
            $error = "The class \"{$class}\" is not declared.";
            
            return false;
        }
    
        if (! method_exists($class, $method))
        {
            $error = "The method \"{$class}::{$method}\" is not declared.";
        
            return false;
        }
        
        return true;
    }
}