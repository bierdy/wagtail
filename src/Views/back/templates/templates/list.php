<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($templates)) : ?>
        <p class="text-end">Templates count: <?= count($templates); ?></p>
        <?php if (! empty($resources_count)) : ?>
            <p class="text-end"><?= anchor(route_to('Wagtail\Controllers\Back\Templates::deleteAll'), 'Delete all templates', ['class' => 'btn btn-danger modal-alert-link', 'data-alert-link-text' => "There are {$resources_count} resources assigned to all templates. To delete all templates first delete all resources."]); ?></p>
        <?php else : ?>
            <p class="text-end"><?= anchor(route_to('Wagtail\Controllers\Back\Templates::deleteAll'), 'Delete all templates', ['class' => 'btn btn-danger modal-confirm-link', 'data-confirm-link-text' => 'Are you sure you want to delete all templates?']); ?></p>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table link-secondary">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Class::method</th>
                        <th>Unique</th>
                        <th>Resources count</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($templates as $template) { ?>
                        <tr>
                            <td><?= $template->id; ?></td>
                            <td>
                                <?php if (! empty($template->icon)) : ?>
                                    <i class="<?= $template->icon; ?>"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="link-secondary text-decoration-none" href="<?= base_url(route_to('Wagtail\Controllers\Back\Templates::edit', $template->id)); ?>"><?= $template->title; ?></a>
                            </td>
                            <td><?= $template->class_method; ?></td>
                            <td>
                                <?php if ($template->unique) : ?>
                                    <i class="bi bi-check"></i>
                                <?php endif ?>
                            </td>
                            <td><?= $template->resources_count; ?></td>
                            <td><?= date('Y.m.d H:i:s', strtotime($template->created_at)); ?></td>
                            <td><?= date('Y.m.d H:i:s', strtotime($template->updated_at)); ?></td>
                            <td>
                                <div class="text-end">
                                    <?php if (empty($template->active)) : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Templates::activate', $template->id)); ?>"><i class="bi bi-toggle-off"></i></a>
                                    <?php else : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Templates::deactivate', $template->id)); ?>"><i class="bi bi-toggle-on"></i></a>
                                    <?php endif; ?>
                                    <?php if (! empty($template->resources_count)) : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Templates::delete', $template->id)); ?>" class="modal-alert-link" data-alert-link-text="There are <?= $template->resources_count; ?> resources with the template &quot;<?= $template->title; ?>&quot;. To delete a template uninstall this template from all resources assigned it."><i class="bi bi-trash link-danger"></i></a>
                                    <?php else : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Templates::delete', $template->id)); ?>" class="modal-confirm-link" data-confirm-link-text="Are you sure you want to delete template &quot;<?= $template->title; ?>&quot;?"><i class="bi bi-trash link-danger"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p>There are no templates.</p>
    <?php endif ?>
    <div class="overflow-hidden">
        <?= anchor(route_to('Wagtail\Controllers\Back\Templates::add'), 'Add template', ['class' => 'btn btn-primary float-end']); ?>
    </div>
<?= $this->endSection(); ?>