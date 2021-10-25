<?php

namespace Wagtail\Models;

class Variable extends Wagtail
{
    protected $table = 'variables';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['title', 'name', 'active', 'class', 'options', 'template', 'validation_rules', 'language_id'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;
    protected $dateFormat = 'datetime';
    
    protected $validationRules = [
        'title' => 'required|is_unique[variables.title,id,{id}]',
        'name' => 'required|is_unique[variables.name,id,{id}]|alpha_dash',
        'class' => 'required|is_class_exist|class_is_not_implement_interface[Wagtail\Variables\VariableInterface]',
        'options' => 'permit_empty|valid_json',
        'template' => 'required',
        'validation_rules' => 'permit_empty|valid_json',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    /*
     * Get variables with their values.
     */
    public function getVariablesWithValues(int $resource_id, int $template_id) : array
    {
        $templateModel = model('Template');
        $variableModel = model('Variable');
        $templateVariableModel = model('TemplateVariable');
        $variableValueModel = model('VariableValue');
        
        $variables = $variableModel
            ->select("{$variableModel->table}.*")
            ->where('t.id', $template_id)
            ->join("{$templateVariableModel->table} AS tv", "tv.variable_id = {$variableModel->table}.id", 'left')
            ->join("{$templateModel->table} AS t", "t.id = tv.template_id", 'left')
            ->orderBy('tv.order', 'ASC')
            ->findAll();
        
        $variable_values = $variableValueModel->where('resource_id', $resource_id)->orderBy('order', 'ASC')->findAll();
        
        return array_map(function($variable) use ($variable_values) {
            $values = [];
            $variable->value = null;
            $variable->values = [];
            
            if (! empty($variable_values))
                foreach($variable_values as $variable_value)
                    if ($variable->id === $variable_value->variable_id)
                        $values[] = $variable_value;
            
            if (count($values) == 1)
                $variable->value = array_shift($values);
            else
                $variable->values = $values;
            
            return $variable;
        }, $variables);
    }
    
    /*
     * Set variable validation rules.
     */
    public function setVariableValidationRules(array $validation_rules)
    {
        $resourceModel = model('Resource');
        
        if (! empty($validation_rules['rules']))
            foreach($validation_rules['rules'] as $validation_name => $validation_rule)
                $resourceModel->setValidationRule($validation_name, $validation_rule);
    }
}