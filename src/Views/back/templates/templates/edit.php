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
        <div class="form-template-variables mb-3">
            <div class="mb-3">
                Variables
            </div>
            <div>
                <?= anchor(route_to('Wagtail\Controllers\Back\VariableGroups::add', $template->id), 'Add variable group', ['class' => 'btn btn-light']); ?>
            </div>
            <div class="mb-3">
                <div class="card-placeholder">
                    <i class="bi bi-caret-right-fill"></i>
                </div>
                <?php if (! empty($template_variable_groups)) : ?>
                    <?php foreach($template_variable_groups as $template_variable_group) : ?>
                        <div class="card active-variables" draggable="true">
                            <?= form_input(['type' => 'hidden', 'name' => "template_variable_groups[{$template_variable_group->id}][id]", 'value' => $template_variable_group->id, 'class' => 'card-id']); ?>
                            <?= form_input(['type' => 'hidden', 'name' => "template_variable_groups[{$template_variable_group->id}][order]", 'value' => $template_variable_group->order, 'class' => 'card-order']); ?>
                            <div class="card-header">
                                <?= $template_variable_group->title; ?>
                                <a href="<?= base_url(route_to('Wagtail\Controllers\Back\VariableGroups::delete', $template_variable_group->id)); ?>" class="modal-confirm-link" data-confirm-link-text="Are you sure you want to delete variable group &quot;<?= $template_variable_group->title; ?>&quot;?">
                                    <i class="bi bi-trash link-danger"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="card-body-placeholder">
                                    <i class="bi bi-caret-right-fill"></i>
                                </div>
                                <?php if (! empty($variable_group_variables)) : ?>
                                    <?php foreach($variable_group_variables as $variable_group_variable) : ?>
                                        <?php if ($variable_group_variable->variable_group_id !== $template_variable_group->id) continue; ?>
                                        <div class="card-item" draggable="true">
                                            <?= $variables[$variable_group_variable->variable_id]->title; ?>
                                            <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable_group_variable->variable_id}][template_variable_id]", 'value' => $template_variables[$variable_group_variable->variable_id]->id ?? 0, 'class' => 'card-item-id']); ?>
                                            <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable_group_variable->variable_id}][checked]", 'value' => 1, 'class' => 'card-item-checked']); ?>
                                            <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable_group_variable->variable_id}][order]", 'value' => $variable_group_variable->order, 'class' => 'card-item-order']); ?>
                                            <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable_group_variable->variable_id}][variable_group_id]", 'value' => $template_variable_group->id, 'class' => 'card-item-variable-group-id']); ?>
                                            <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable_group_variable->variable_id}][variable_group_id_original]", 'value' => $template_variable_group->id, 'class' => 'card-item-variable-group-id-original']); ?>
                                        </div>
                                        <div class="card-body-placeholder">
                                            <i class="bi bi-caret-right-fill"></i>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-placeholder">
                            <i class="bi bi-caret-right-fill"></i>
                        </div>
                    <?php endforeach; ?>
                    <hr class="mt-0">
                <?php endif; ?>
                <div class="card hidden-variables mb-3">
                    <div class="card-header">
                        Hidden variables
                    </div>
                    <div class="card-body">
                        <div class="card-body-placeholder">
                            <i class="bi bi-caret-right-fill"></i>
                        </div>
                        <?php if (! empty($template_variables)) : ?>
                            <?php foreach($template_variables as $template_variable) : ?>
                                <?php if (in_array($template_variable->variable_id, array_column($variable_group_variables, 'variable_id'))) continue; ?>
                                <div class="card-item" draggable="true">
                                    <?= $variables[$template_variable->variable_id]->title; ?>
                                    <?= form_input(['type' => 'hidden', 'name' => "variables[{$template_variable->variable_id}][template_variable_id]", 'value' => $template_variables[$template_variable->variable_id]->id ?? 0, 'class' => 'card-item-id']); ?>
                                    <?= form_input(['type' => 'hidden', 'name' => "variables[{$template_variable->variable_id}][checked]", 'value' => 1, 'class' => 'card-item-checked']); ?>
                                    <?= form_input(['type' => 'hidden', 'name' => "variables[{$template_variable->variable_id}][order]", 'value' => '', 'class' => 'card-item-order']); ?>
                                    <?= form_input(['type' => 'hidden', 'name' => "variables[{$template_variable->variable_id}][variable_group_id]", 'value' => '', 'class' => 'card-item-variable-group-id']); ?>
                                </div>
                                <div class="card-body-placeholder">
                                    <i class="bi bi-caret-right-fill"></i>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <hr>
                <div class="card available-variables mb-3">
                    <div class="card-header">
                        Available variables
                    </div>
                    <div class="card-body">
                        <div class="card-body-placeholder">
                            <i class="bi bi-caret-right-fill"></i>
                        </div>
                        <?php if (! empty($variables)) : ?>
                                <?php foreach($variables as $variable_id => $variable) : ?>
                                    <?php if (isset($template_variables[$variable->id])) continue; ?>
                                    <div class="card-item" draggable="true">
                                        <?= $variable->title; ?>
                                        <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable->id}][template_variable_id]", 'value' => 0, 'class' => 'card-item-id']); ?>
                                        <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable->id}][checked]", 'value' => '', 'class' => 'card-item-checked']); ?>
                                        <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable->id}][order]", 'value' => '', 'class' => 'card-item-order']); ?>
                                        <?= form_input(['type' => 'hidden', 'name' => "variables[{$variable->id}][variable_group_id]", 'value' => '', 'class' => 'card-item-variable-group-id']); ?>
                                    </div>
                                    <div class="card-body-placeholder">
                                        <i class="bi bi-caret-right-fill"></i>
                                    </div>
                                <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Wagtail\Controllers\Back\Templates::list'), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Update', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>