<?php

namespace Wagtail\Models;

class TemplateVariable extends Wagtail
{
    protected $table = 'template_variables';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['template_id', 'variable_id', 'order'];
    
    protected $useTimestamps = true;
    protected $createdField = null;
    protected $updatedField = null;
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'order' => 'required|numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}