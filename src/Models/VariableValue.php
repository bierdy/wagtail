<?php

namespace Wagtail\Models;

class VariableValue extends Wagtail
{
    protected $table = 'variable_values';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['resource_id', 'variable_id', 'value', 'order'];
    
    protected $useTimestamps = true;
    protected $createdField = null;
    protected $updatedField = null;
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'resource_id' => 'required',
        'variable_id' => 'required',
        'order' => 'required|numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}