<?= $this->extend('Velldoris\Views\back\templates\default'); ?>

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
        <?php if (! empty($variables_options)) : ?>
            <div class="mb-3">
                Variables
                <?php foreach($variables_options as $variable_id => $variable_title) : ?>
                    <div class="form-check d-flex align-items-center mt-2">
                        <?= form_checkbox("variables[{$variable_id}][checked]", true, $post['variables'][$variable_id]['checked'] ?? false, ['class' => 'form-check-input mb-1' , 'id' => "variable_{$variable_id}"]); ?>
                        <?= form_label($variable_title, "variable_{$variable_id}", ['class' => 'form-check-label mx-2']); ?>
                        <?= form_input("variables[{$variable_id}][order]", $post['variables'][$variable_id]['order'] ?? 1000, ['class' => 'form-control form-control-sm w-auto']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Velldoris\Controllers\Back\Templates::list'), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Add', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>