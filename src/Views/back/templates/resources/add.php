<?= $this->extend('Velldoris\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($errors)) : ?>
        <div class="alert alert-danger">
            <?= $message; ?>
            <?php foreach ($errors as $error) : ?>
                <div><?= $error; ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?= form_open_multipart(); ?>
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? '', ['class' => 'form-control', 'id' => 'title']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Url', 'url', ['class' => 'form-label']); ?>
            <?= form_input('url', $post['url'] ?? '', ['class' => 'form-control', 'id' => 'url']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Parent', 'parent_title', ['class' => 'form-label']); ?>
            <?= form_input('parent_title', $parent->title ?? 'Root', ['class' => 'form-control', 'id' => 'parent_title', 'disabled' => 'disabled']); ?>
            <?= form_hidden('parent_id', $parent->id ?? 0); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Template', 'template_id', ['class' => 'form-label']); ?>
            <?= form_dropdown('template_id', $templates_options, $post['template_id'] ?? '', ['class' => 'form-control' , 'id' => 'template_id']); ?>
        </div>
        <div class="mb-3 overflow-hidden">
            <?= form_submit('submit', 'Add', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>