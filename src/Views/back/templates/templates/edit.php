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
    <?php elseif (! empty($message)) : ?>
        <div class="alert alert-success">
            <?= $message; ?>
        </div>
    <?php endif ?>
    <?= form_open(); ?>
        <?= form_hidden('id', $template->id ?? 0); ?>
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? $template->title ?? '', ['class' => 'form-control' , 'id' => 'title']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Icon', 'icon', ['class' => 'form-label']); ?>
            <?php $icon = $post['icon'] ?? $template->icon ?? ''; ?>
            <?php if (! empty($icon)) : ?>
                <i class="<?= $icon; ?> link-secondary d-block mb-3"></i>
            <?php endif; ?>
            <?= form_input('icon', $post['icon'] ?? $template->icon ?? '', ['class' => 'form-control' , 'id' => 'icon']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Class::method', 'class_method', ['class' => 'form-label']); ?>
            <?= form_input('class_method', $post['class_method'] ?? $template->class_method ?? '', ['class' => 'form-control' , 'id' => 'class_method']); ?>
        </div>
        <div class="form-check mb-3">
            <?= form_hidden('unique', false); ?>
            <?= form_checkbox('unique', true, $post['unique'] ?? $template->unique ?? false, ['class' => 'form-check-input mb-1' , 'id' => 'unique']); ?>
            <?= form_label('Unique', 'unique', ['class' => 'form-label']); ?>
        </div>
        <?php if (! empty($variables_options)) : ?>
            <div class="mb-3">
                Variables
                <?php foreach($variables_options as $variable_id => $variable_title) : ?>
                    <div class="form-check d-flex align-items-center mt-2">
                        <?= form_hidden("variables[{$variable_id}][id]", $template_variables_options[$variable_id]->id ?? 0); ?>
                        <?= form_checkbox("variables[{$variable_id}][checked]", true, ! empty($post['variables'][$variable_id]['checked']) || isset($template_variables_options[$variable_id]), ['class' => 'form-check-input mb-1' , 'id' => "variable_{$variable_id}"]); ?>
                        <?= form_label($variable_title, "variable_{$variable_id}", ['class' => 'form-check-label mx-2']); ?>
                        <?= form_input("variables[{$variable_id}][order]", $post['variables'][$variable_id]['order'] ?? $template_variables_options[$variable_id]->order ?? 1000, ['class' => 'form-control form-control-sm w-auto']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Wagtail\Controllers\Back\Templates::list'), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Update', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>