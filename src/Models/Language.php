<?php

namespace Velldoris\Models;

class Language extends Velldoris
{
    protected $table = 'languages';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['title', 'code', 'icon', 'default', 'active', 'order'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'title' => 'required|is_unique[languages.title,id,{id}]',
        'code' => 'is_unique[languages.code,id,{id}]',
        'icon' => 'required',
        'order' => 'required|numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}