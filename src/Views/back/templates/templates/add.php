<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($errors)) : ?>
        <div class="alert alert-danger">
            <?= $message; ?>
            <?php foreach ($errors as $error) : ?>
                <?php if (is_array($error)) : ?>
                    <?php foreach ($error as $error_filed_id => $error_filed) : ?>
                        <?php foreach ($error_filed as $error_filed_value) : ?>
                            <div><?= $error_filed_value; ?></div>
                        <?php endforeach ?>
                    <?php endforeach ?>
                <?php else : ?>
                    <div><?= $error; ?></div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?= form_open(); ?>
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? '', ['class' => 'form-control' , 'id' => 'title']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Icon', 'icon', ['class' => 'form-label']); ?>
            <?= form_input('icon', $post['icon'] ?? '', ['class' => 'form-control' , 'id' => 'icon']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Class::method', 'class_method', ['class' => 'form-label']); ?>
            <?= form_input('class_method', $post['class_method'] ?? '', ['class' => 'form-control' , 'id' => 'class_method']); ?>
        </div>
        <div class="form-check mb-3">
            <?= form_checkbox('unique', true, $post['unique'] ?? false, ['class' => 'form-check-input mb-1' , 'id' => 'unique']); ?>
            <?= form_label('Unique', 'unique', ['class' => 'form-label']); ?>
        </div>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Wagtail\Controllers\Back\Templates::list'), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Add', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>