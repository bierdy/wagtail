<?php

$resourceModel = model('Resource');
$options = json_decode($variable->options);

$select_options = (array) ($options->options ?? (object) []);
$empty_option_title = $options->empty_option_title ?? 'Select value';

$select_options = ['' => $empty_option_title] + $select_options;

?>

<div class="mb-3">
    <?= form_label($variable->title, $variable->name, ['class' => 'form-label']); ?>
    <?= form_dropdown($variable->name, $select_options, $post[$variable->name] ?? $variable->value->value ?? '', ['class' => 'form-select', 'id' => $variable->name]); ?>
</div>