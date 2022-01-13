<?php

$resourceModel = model('Resource');
$options = json_decode($variable->options);

$parent_id = $options->parent_id ?? 0;
$parent_id = (int) $parent_id;
$template_ids = $options->template_ids ?? '';
$template_ids = explode(',', $template_ids);
$template_ids = array_diff($template_ids, ['']);
$active = $options->active ?? false;
$active = (bool) $active;
$empty_option_title = $options->empty_option_title ?? 'Select resource';

$resources = $resourceModel->getResourceTree($parent_id);
$variable_resources = [];

$buildVariableResourcesArray = function(array $childs, int $tree_level = 0, array $result = []) use (&$buildVariableResourcesArray, $template_ids, $active) : array
{
    foreach($childs as $child)
    {
        if (! empty($template_ids) && ! in_array($child->template_id, $template_ids))
            continue;
    
        if ($active && ! $child->active)
            continue;
        
        $result[$child->id] = str_repeat('.', $tree_level * 3) . $child->title;
        
        if (! empty($child->childs))
            $result = $result + $buildVariableResourcesArray($child->childs, $tree_level + 1);
    }
    
    return $result;
};

$variable_resources = $buildVariableResourcesArray($resources[0]->childs ?? [], 0);
$variable_resources = ['' => $empty_option_title] + $variable_resources;

?>

<div class="mb-3">
    <?= form_label($variable->title, $variable->name, ['class' => 'form-label']); ?>
    <?= form_dropdown($variable->name, $variable_resources, $post[$variable->name] ?? $variable->value->value ?? '', ['class' => 'form-select', 'id' => $variable->name]); ?>
</div>