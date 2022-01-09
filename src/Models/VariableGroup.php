<?php

namespace Wagtail\Models;

class VariableGroup extends Wagtail
{
    protected $table = 'variable_groups';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['title'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'title' => 'required',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}