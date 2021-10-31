<?php

namespace Wagtail\Models;

class Template extends Wagtail
{
    protected $table = 'templates';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['title', 'icon', 'class_method', 'active', 'unique'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'title' => 'required|is_unique[templates.title,id,{id}]',
        'class_method' => 'permit_empty|is_class_exist|is_method_exist',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    /*
     * Get non-unique templates plus unique templates that are not assigned on any resource.
     */
    public function getAvailableTemplates() : array
    {
        $resourceModel = model('Resource');
        
        return $this
            ->select("{$this->table}.*")
            ->whereNotIn('id', function ($builder) use ($resourceModel) {
                return $builder
                    ->select('r.template_id')
                    ->from($resourceModel->table . ' AS r')
                    ->where('t.unique', 1)
                    ->join("{$this->table} AS t", 't.id = r.template_id', 'left');
            })
            ->findAll();
    }
}