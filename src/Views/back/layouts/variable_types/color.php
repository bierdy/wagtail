<div class="mb-3">
    <?= form_label($variable->title, $variable->name, ['class' => 'form-label']); ?>
    <?= form_input(['type' => 'color', 'name' => $variable->name, 'value' => $post[$variable->name] ?? $variable->value->value ?? '', 'class' => 'form-control form-control-color', 'id' => $variable->name]); ?>
</div>