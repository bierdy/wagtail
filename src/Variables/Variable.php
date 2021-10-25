<?php

namespace Wagtail\Variables;

class Variable
{
    protected $variableModel;
    protected $variableValueModel;
    protected $post;
    protected $variable;
    protected $resource;
    
    public function __construct(array $post, object $resource, object $variable)
    {
        $this->variableModel = model('Variable');
        $this->variableValueModel = model('VariableValue');
        $this->post = $post;
        $this->resource = $resource;
        $this->variable = $variable;
    }
}