<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($errors)) : ?>
        <div class="alert alert-danger">
            <?= $message; ?>
            <?php foreach ($errors as $error) : ?>
                <div><?= $error; ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?= form_open(); ?>
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? '', ['class' => 'form-control' , 'id' => 'title']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Name', 'name', ['class' => 'form-label']); ?>
            <?= form_input('name', $post['name'] ?? '', ['class' => 'form-control' , 'id' => 'name']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Class', 'class', ['class' => 'form-label']); ?>
            <?= form_input('class', $post['class'] ?? '', ['class' => 'form-control' , 'id' => 'class']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Options', 'options', ['class' => 'form-label']); ?>
            <?= form_textarea('options', $post['options'] ?? '', ['class' => 'form-control' , 'id' => 'options']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Template', 'template', ['class' => 'form-label']); ?>
            <?= form_input('template', $post['template'] ?? '', ['class' => 'form-control' , 'id' => 'template']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Validation rule', 'validation_rules', ['class' => 'form-label']); ?>
            <?= form_textarea('validation_rules', $post['validation_rules'] ?? '', ['class' => 'form-control' , 'id' => 'validation_rules']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Language', 'language_id', ['class' => 'form-label']); ?>
            <?= form_dropdown('language_id', $languages_options, $post['language_id'] ?? '', ['class' => 'form-control' , 'id' => 'language_id']); ?>
        </div>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Wagtail\Controllers\Back\Variables::list'), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Add', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>