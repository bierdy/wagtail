<?php

namespace Wagtail\Variables;

class Text extends Variable implements VariableInterface
{
    public function init()
    {
        if (! isset($this->post[$this->variable->name]) && ! empty($this->variable->value))
            $this->variableValueModel->delete($this->variable->value->id);
        
        elseif (isset($this->post[$this->variable->name]) && ! empty($this->variable->value))
            $this->variableValueModel->update($this->variable->value->id, ['value' => $this->post[$this->variable->name]]);
        
        elseif (isset($this->post[$this->variable->name]) && empty($this->variable->value))
            $this->variableValueModel->insert(['resource_id' => $this->resource->id, 'variable_id' => $this->variable->id, 'value' => $this->post[$this->variable->name], 'order' => 0]);
    }
}