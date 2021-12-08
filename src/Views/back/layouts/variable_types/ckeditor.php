<div class="mb-3">
    <?= form_label($variable->title, $variable->name, ['class' => 'form-label']); ?>
    <?= form_textarea($variable->name, $post[$variable->name] ?? $variable->value->value ?? '', ['class' => 'form-control ck-editor', 'id' => $variable->name]); ?>
</div>