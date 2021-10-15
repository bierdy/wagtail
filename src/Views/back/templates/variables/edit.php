<?= $this->extend('Velldoris\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($errors)) : ?>
        <div class="alert alert-danger">
            <?= $message; ?>
            <?php foreach ($errors as $error) : ?>
                <div><?= $error; ?></div>
            <?php endforeach ?>
        </div>
    <?php elseif (! empty($message)) : ?>
        <div class="alert alert-success">
            <?= $message; ?>
        </div>
    <?php endif ?>
    <?= form_open(); ?>
        <?= form_hidden('id', $variable->id ?? 0); ?>
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? $variable->title ?? '', ['class' => 'form-control' , 'id' => 'title']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Name', 'name', ['class' => 'form-label']); ?>
            <?= form_input('name', $post['name'] ?? $variable->name ?? '', ['class' => 'form-control' , 'id' => 'name']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Class', 'class', ['class' => 'form-label']); ?>
            <?= form_input('class', $post['class'] ?? $variable->class ?? '', ['class' => 'form-control' , 'id' => 'class']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Options', 'options', ['class' => 'form-label']); ?>
            <?= form_textarea('options', $post['options'] ?? $variable->options ?? '', ['class' => 'form-control' , 'id' => 'options']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Template', 'template', ['class' => 'form-label']); ?>
            <?= form_input('template', $post['template'] ?? $variable->template ?? '', ['class' => 'form-control' , 'id' => 'template']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Validation rule', 'validation_rules', ['class' => 'form-label']); ?>
            <?= form_textarea('validation_rules', $post['validation_rules'] ?? $variable->validation_rules ?? '', ['class' => 'form-control' , 'id' => 'validation_rules']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Language', 'language_id', ['class' => 'form-label']); ?>
            <?= form_dropdown('language_id', $languages_options, $post['language_id'] ?? $variable->language_id ?? '', ['class' => 'form-control' , 'id' => 'language_id']); ?>
        </div>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Velldoris\Controllers\Back\Variables::list'), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Update', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>