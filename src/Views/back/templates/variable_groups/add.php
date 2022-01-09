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
        <?= form_hidden('template_id', $template_id ?? 0); ?>
        <?= form_hidden('order', $template_variable_groups_count ?? 0); ?>
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? '', ['class' => 'form-control', 'id' => 'title']); ?>
        </div>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Wagtail\Controllers\Back\Templates::edit', $template_id), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Add', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>