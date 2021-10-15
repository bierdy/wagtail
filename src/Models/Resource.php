<?php

namespace Velldoris\Models;

class Resource extends Velldoris
{
    protected $table = 'resources';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['parent_id', 'template_id', 'title', 'url', 'order', 'active'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'title' => 'required|is_unique[resources.title,id,{id}]',
        'parent_id' => 'required|numeric',
        'template_id' => 'required|numeric',
        'url' => 'is_unique[resources.url,id,{id}]',
        'order' => 'numeric',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    public function getResourcesTree() : array
    {
        $resources = $this->getResources();
        
        if (empty($resources))
            return [];
        
        $resources = $this->addAvailableTemplates($resources);
        
        return $this->buildTree($resources);
    }
    
    public function getResourceTree(int $id) : array
    {
        $templateModel = model('Template');
        
        if (is_null($resource = $this
            ->select("{$this->table}.*, t.title AS template_title, t.icon AS template_icon, t.unique AS template_unique")
            ->join("{$templateModel->table} AS t", "t.id = {$this->table}.template_id", 'left')
            ->find($id)))
            return [];
        
        $resource_childs = $this->getResourceChilds($id);
        $resources = $resource_childs;
        array_unshift($resources, $resource);
        
        $resources = $this->addAvailableTemplates($resources);
        
        return $this->buildTree($resources);
    }
    
    protected function getResources() : array
    {
        $templateModel = model('Template');
    
        return $this
            ->select("{$this->table}.*")
            ->select("t.title AS template_title")
            ->select("t.icon AS template_icon")
            ->select("t.unique AS template_unique")
            ->join("{$templateModel->table} AS t", "t.id = {$this->table}.template_id", 'left')
            ->orderBy("{$this->table}.order", 'ASC')
            ->groupBy("{$this->table}.id")
            ->findAll();
    }
    
    public function getResourceChilds(int $id) : array
    {
        $velldorisModel = model('Velldoris');
        $templateModel = model('Template');
        
        $query = "
            WITH RECURSIVE cte_{$this->table} AS (
                SELECT
                    r1.*
                FROM
                    {$this->table} AS r1
                WHERE
                    r1.parent_id = {$id}
                UNION
                    ALL
                SELECT
                    r2.*
                FROM
                    {$this->table} AS r2
                INNER JOIN
                    cte_{$this->table}
                ON
                    r2.parent_id = cte_{$this->table}.id
            )
            SELECT
                cte_{$this->table}.*, t.title AS template_title, t.icon AS template_icon, t.unique AS template_unique
            FROM
                cte_{$this->table}
            LEFT JOIN
                {$templateModel->table} AS t
            ON
                t.id = cte_{$this->table}.template_id
            ORDER BY
                cte_{$this->table}.order;
        ";
    
        return $velldorisModel->db->query($query)->getResult();
    }
    
    protected function addAvailableTemplates($resources) : array
    {
        $templateModel = model('Template');
        
        $available_templates = $templateModel->getAvailableTemplates();
        
        if (! empty($available_templates))
            $available_templates = array_combine(array_column($available_templates, 'id'), $available_templates);
        
        return array_map(function($resource) use ($available_templates) {
            if (isset($available_templates[$resource->template_id]))
                unset($available_templates[$resource->template_id]);
            
            $resource->available_templates = $available_templates;
        
            return $resource;
        }, $resources);
    }
    
    protected function buildTree(array $resources = []) : array
    {
        $childs = [];
        
        foreach($resources as $resource)
            $childs[$resource->parent_id ?? 0][] = $resource;
        
        foreach($resources as $resource)
            if (isset($childs[$resource->id]))
                $resource->childs = $childs[$resource->id];
        
        return $childs[0] ?? [];
    }
}