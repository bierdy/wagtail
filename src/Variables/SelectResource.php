<?php

namespace Wagtail\Variables;

class SelectResource extends Variable implements VariableInterface
{
    public function init()
    {
        if (empty($this->post[$this->variable->name]) && ! empty($this->variable->value))
            $this->variableValueModel->delete($this->variable->value->id);
        
        elseif (! empty($this->post[$this->variable->name]) && ! empty($this->variable->value))
            $this->variableValueModel->update($this->variable->value->id, ['value' => $this->post[$this->variable->name]]);
        
        elseif (! empty($this->post[$this->variable->name]) && empty($this->variable->value))
            $this->variableValueModel->insert(['resource_id' => $this->resource->id, 'variable_id' => $this->variable->id, 'value' => $this->post[$this->variable->name], 'order' => 0]);
    }
}