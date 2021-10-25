<?php

namespace Wagtail\Models;

class Resource extends Wagtail
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
        $wagtailModel = model('Wagtail');
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
    
        return $wagtailModel->db->query($query)->getResult();
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
    
    public function getResourceTreeByUriSegments(array $uri_segments = []) : ? object
    {
        if (empty($uri_segments))
            return null;
        
        $templateModel = model('Template');
        $variableModel = model('Variable');
        $variableValueModel = model('VariableValue');
        $uri_segments_resources = [];
        $parent_id = null;
        $resource = null;
        
        foreach($uri_segments as $uri_segment)
        {
            $uri_segment_resource = $this
                ->select("{$this->table}.*, t.title AS template_title, t.class_method AS template_class_method")
                ->where("{$this->table}.url", $uri_segment)
                ->where("{$this->table}.parent_id", $parent_id ?? 0)
                ->join("{$templateModel->table} AS t", "t.id = {$this->table}.template_id", 'left')
                ->findAll();
            
            if (count($uri_segment_resource) !== 1)
                return null;
    
            $uri_segment_resource = $uri_segment_resource[0];
            
            $uri_segment_resource->variables = $variableModel
                ->select('variables.*, template_variables.order')
                ->where('template_variables.template_id', $uri_segment_resource->template_id)
                ->join('template_variables', 'template_variables.variable_id = variables.id', 'left')
                ->orderBy('template_variables.order', 'ASC')
                ->findAll();
    
            foreach($uri_segment_resource->variables as &$resource_variable)
            {
                $resource_variable->values = $variableValueModel
                    ->select('variable_values.*')
                    ->where('variable_values.resource_id', $uri_segment_resource->id)
                    ->where('variable_values.variable_id', $resource_variable->id)
                    ->orderBy('variable_values.order', 'ASC')
                    ->findAll();
            }
    
            $uri_segments_resources[] = $uri_segment_resource;
            $parent_id = $uri_segment_resource->id;
        }
    
        $uri_segment_resource_ = null;
        foreach($uri_segments_resources as $key => $uri_segment_resource)
        {
            if ($key === array_key_first($uri_segments_resources))
            {
                $uri_segment_resource_ = $uri_segment_resource;
                $uri_segment_resource_->parent = null;
                continue;
            }
    
            $resource = $uri_segment_resource;
            $resource->parent = $uri_segment_resource_;
            $uri_segment_resource_ = $uri_segment_resource;
        }
        
        unset($uri_segments);
        unset($uri_segment);
        unset($uri_segments_resources);
        unset($uri_segment_resource);
        unset($uri_segment_resource_);
        unset($parent_id);
        
        return $resource;
    }
    
    public function getResourceBranchSegments(object $resource_branch, array $resource_branch_segments = []) : array
    {
        $resource_branch_segments[] = $resource_branch->url;
        
        if (! empty($resource_branch->parent))
            $resource_branch_segments = $this->getResourceBranchSegments($resource_branch->parent, $resource_branch_segments);
        
        return $resource_branch_segments;
    }
    
    public function updateUrl(int $id = 0)
    {
        $resource = $this->find($id);
        $resource_neighbors = $this->where('parent_id', $resource->parent_id)->where('id !=', $id)->findAll();
        $resource_neighbors_urls = array_column($resource_neighbors, 'url');
        $wagtail_app_config = config('WagtailApp');
    
        $resource_url = ! empty($resource->url) ? $resource->url : mb_url_title($resource->title, $wagtail_app_config->resourceUrlSeparator, true);
        
        while(in_array($resource_url, $resource_neighbors_urls))
            $resource_url .= $wagtail_app_config->resourceUrlSeparator . $wagtail_app_config->resourceUrlCopyPostfix;
        
        $this->update($id, ['url' => $resource_url]);
    }
}