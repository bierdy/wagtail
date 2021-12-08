<div class="mb-3">
    <?= form_label($variable->title, $variable->name, ['class' => 'form-label']); ?>
    <?= form_input($variable->name, $post[$variable->name] ?? $variable->value->value ?? '', ['class' => 'form-control', 'id' => $variable->name, 'disabled' => 'disabled']); ?>
</div>