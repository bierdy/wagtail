<?php

namespace Velldoris\Validation\Rules;

class ClassRules
{
    /**
     * Validate the existence of the class
     *
     * @param string $class
     * @param string|null $error
     *
     * @return bool
     */
    public function is_class_exist(string $class, string &$error = null) : bool
    {
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
     * @param string $class
     * @param string $interface
     * @param array $data
     * @param string|null $error
     *
     * @return bool
     */
    public function class_is_not_implement_interface(string $class, string $interface, array $data = [], string &$error = null) : bool
    {
        $class_interfaces = class_implements($class);
        
        if (! isset($class_interfaces[$interface]))
        {
            $error = "The class \"{$class}\" must implement the interface \"{$interface}\".";
    
            return false;
        }
        
        return true;
    }
}