<?php

namespace Wagtail\Models;

class TemplateVariableGroup extends Wagtail
{
    protected $table = 'template_variable_groups';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['template_id', 'variable_group_id', 'order'];
    
    protected $useTimestamps = true;
    protected $createdField = null;
    protected $updatedField = null;
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'template_id' => 'required',
        'variable_group_id' => 'required',
        'order' => 'required|numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}