<?php

namespace Wagtail\Models;

class VariableGroupVariable extends Wagtail
{
    protected $table = 'variable_group_variables';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['variable_group_id', 'variable_id', 'order'];
    
    protected $useTimestamps = true;
    protected $createdField = null;
    protected $updatedField = null;
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'variable_group_id' => 'required',
        'variable_id' => 'required',
        'order' => 'required|numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}