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
    
    public function getReverseResourceTree(int $id) : ? object
    {
        $resource_table = $this->db->prefixTable($this->table);
        
        $query = "
            WITH RECURSIVE
                cte_resources AS (
                    SELECT
                        1 AS level,
                        childs.*
                    FROM
                        {$resource_table} AS childs
                    WHERE
                        childs.id = '{$id}'
                    UNION ALL
                    SELECT
                        level + 1,
                        parents.*
                    FROM
                        cte_resources AS childs
                    INNER JOIN
                        {$resource_table} AS parents
                    ON
                        parents.id = childs.parent_id
                )
            SELECT
                resources.*
            FROM
                cte_resources AS resources
            ORDER BY
                level DESC
        ";
        
        if (empty($resources = $this->db->query($query)->getResult()))
            return null;
    
        $resources_uri_segments = array_column($resources, 'url');
    
        return $this->getResourceTreeByUriSegments($resources_uri_segments);
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
        
        $resource_table = $this->db->prefixTable($this->table);
        $template_table = $templateModel->db->prefixTable($templateModel->table);
        
        $query = "
            WITH RECURSIVE cte_{$resource_table} AS (
                SELECT
                    r1.*
                FROM
                    {$resource_table} AS r1
                WHERE
                    r1.parent_id = {$id}
                UNION
                    ALL
                SELECT
                    r2.*
                FROM
                    {$resource_table} AS r2
                INNER JOIN
                    cte_{$resource_table}
                ON
                    r2.parent_id = cte_{$resource_table}.id
            )
            SELECT
                cte_{$resource_table}.*, t.title AS template_title, t.icon AS template_icon, t.unique AS template_unique
            FROM
                cte_{$resource_table}
            LEFT JOIN
                {$template_table} AS t
            ON
                t.id = cte_{$resource_table}.template_id
            ORDER BY
                cte_{$resource_table}.order;
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
    
    public function getResourceTreeByUriSegments(array $uri_segments = []) : ? object
    {
        if (empty($uri_segments))
            return null;
    
        $tree = null;
        $last_uri_segment = $uri_segments[array_key_last($uri_segments)];
        $uri_segments_reverse = array_reverse($uri_segments);
        $uri_segments_reverse_json = json_encode($uri_segments_reverse);
        
        $templateModel = model('Template');
        $templateVariableModel = model('TemplateVariable');
        $variableModel = model('Variable');
        $variableValueModel = model('VariableValue');
    
        $resource_table = $this->db->prefixTable($this->table);
        $template_table = $this->db->prefixTable($templateModel->table);
        $template_variable_table = $this->db->prefixTable($templateVariableModel->table);
        $variable_table = $this->db->prefixTable($variableModel->table);
        $variable_value_table = $this->db->prefixTable($variableValueModel->table);
    
        $query = "
            WITH RECURSIVE
                cte_resources AS (
                    SELECT
                        1 AS level,
                        childs.*
                    FROM
                        {$resource_table} AS childs
                    WHERE
                        childs.url = '{$last_uri_segment}'
                    UNION ALL
                    SELECT
                        level + 1,
                        parents.*
                    FROM
                        cte_resources AS childs
                    INNER JOIN
                        {$resource_table} AS parents
                    ON
                        parents.id = childs.parent_id
                    WHERE
                        parents.url = JSON_UNQUOTE(JSON_EXTRACT('{$uri_segments_reverse_json}', CONCAT('$[', level, ']')))
                )
            SELECT
                resources.*
            FROM
                cte_resources AS resources
            ORDER BY
                level DESC
        ";
        
        $resources = $this->db->query($query)->getResult();
    
        if (empty($resources))
            return null;
    
        // Remove unnecessary resources
        $parent_id = $resources[array_key_first($resources)]->id;
        $resources = array_filter($resources, function($resource, $index) use (&$parent_id) {
            if ($index === 0)
                return true;
            
            if ($resource->parent_id !== $parent_id)
                return false;
            
            $parent_id = $resource->id;
            return true;
        }, ARRAY_FILTER_USE_BOTH);
        // /Remove unnecessary resources
        
        if (count($resources) !== count($uri_segments))
            return null;
    
        $resources = array_values($resources);
    
        $resources_ids = array_column($resources, 'id');
    
        $resources_template_ids = array_column($resources, 'template_id');
        $resources_template_ids = array_unique($resources_template_ids, SORT_NUMERIC);
        
        // Get templates
        $templates = $templateModel
            ->select("{$template_table}.id, {$template_table}.title, {$template_table}.class_method, GROUP_CONCAT(DISTINCT tv.variable_id) AS variables")
            ->join("{$template_variable_table} AS tv", "tv.template_id = {$template_table}.id", 'left')
            ->whereIn("{$template_table}.id", $resources_template_ids)
            ->groupBy("{$template_table}.id")
            ->find();
    
        $templates = array_combine(array_column($templates, 'id'), $templates);
        // /Get templates
    
        // Get variable values
        $variable_values = $variableValueModel
            ->select("{$variable_value_table}.resource_id, {$variable_value_table}.variable_id, {$variable_value_table}.value, {$variable_value_table}.order, v.name AS variable_name, v.options AS variable_options")
            ->join("{$variable_table} AS v", "v.id = {$variable_value_table}.variable_id", 'left')
            ->whereIn("{$variable_value_table}.resource_id", $resources_ids)
            ->orderBy("{$variable_value_table}.order")
            ->find();
        // /Get variable values
        
        // Build tree
        $resource_ = null;
        foreach($resources as $key => $resource)
        {
            $resource->template_title = $templates[$resource->template_id]->title;
            $resource->template_class_method = $templates[$resource->template_id]->class_method;
    
            $template_variables = explode(',', $templates[$resource->template_id]->variables);
            $resource->variables = [];
            foreach($variable_values as &$variable_value)
                if (isset($variable_value->resource_id) && $variable_value->resource_id === $resource->id && in_array($variable_value->variable_id, $template_variables))
                {
                    unset($variable_value->resource_id);
                    $resource->variables[] = $variable_value;
                }
            
            if ($key === array_key_first($resources))
            {
                $resource_ = $resource;
                $resource_->parent = null;
                $tree = $resource_;
                continue;
            }
    
            $tree = $resource;
            $tree->parent = $resource_;
            $resource_ = $resource;
        }
        // /Build tree
        
        return $tree;
    }
    
    protected function buildTree(array $resources = []) : array
    {
        $childs = [];
        
        foreach($resources as $resource)
            $childs[$resource->parent_id ?? 0][] = $resource;
        
        foreach($resources as $resource)
            if (isset($childs[$resource->id]))
                $resource->childs = $childs[$resource->id];
        
        if (empty($childs))
            return [];
        
        return $childs[0] ?? $childs[array_key_first($childs)];
    }
    
    public function updateUrl(int $id = 0)
    {
        $resource = $this->find($id);
        $resource_neighbors = $this->where('parent_id', $resource->parent_id)->where('id !=', $id)->findAll();
        $resource_neighbors_urls = array_column($resource_neighbors, 'url');
        $wagtail_app_config = config('WagtailApp');
        
        if ($resource->url === '/' && (count($this->where('url', '/')->findAll()) <= 1))
            return;
        
        $resource_url = mb_url_title(! empty($resource->url) ? $resource->url : $resource->title, $wagtail_app_config->resourceUrlSeparator, true);
        $resource_url = ! empty($resource_url) ? $resource_url : $wagtail_app_config->resourceUrlEmpty;
        
        while(in_array($resource_url, $resource_neighbors_urls))
            $resource_url .= $wagtail_app_config->resourceUrlSeparator . $wagtail_app_config->resourceUrlCopyPostfix;
        
        $this->update($id, ['url' => $resource_url]);
    }
}