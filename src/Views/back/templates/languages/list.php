<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($languages)) : ?>
        <p class="text-end">Languages count: <?= count($languages); ?></p>
        <?php if (! empty($variables_count)) : ?>
            <p class="text-end"><?= anchor(route_to('Wagtail\Controllers\Back\Languages::deleteAll'), 'Delete all languages', ['class' => 'btn btn-danger modal-alert-link', 'data-alert-link-text' => "There are {$variables_count} variables that has the language assigned. To delete all languages first unassigned all languages from all variables."]); ?></p>
        <?php else : ?>
            <p class="text-end"><?= anchor(route_to('Wagtail\Controllers\Back\Languages::deleteAll'), 'Delete all languages', ['class' => 'btn btn-danger modal-confirm-link', 'data-confirm-link-text' => 'Are you sure you want to delete all languages?']); ?></p>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table link-secondary">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Code</th>
                        <th>Default</th>
                        <th>Order</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($languages as $language) { ?>
                        <tr>
                            <td><?= $language->id; ?></td>
                            <td>
                                <?php if (! empty($language->icon)) : ?>
                                    <?php if (file_exists(FCPATH . trim($language->icon, '/'))) : ?>
                                        <img src="<?= $language->icon; ?>" height="24">
                                    <?php else : ?>
                                        Not found!
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a class="link-secondary text-decoration-none" href="<?= base_url(route_to('Wagtail\Controllers\Back\Languages::edit', $language->id)); ?>"><?= $language->title; ?></a>
                            </td>
                            <td><?= $language->code; ?></td>
                            <td>
                                <?php if ($language->default) : ?>
                                    <i class="bi bi-check"></i>
                                <?php endif ?>
                            </td>
                            <td><?= $language->order; ?></td>
                            <td><?= date('Y.m.d H:i:s', strtotime($language->created_at)); ?></td>
                            <td><?= date('Y.m.d H:i:s', strtotime($language->updated_at)); ?></td>
                            <td>
                                <div class="text-end">
                                    <?php if (empty($language->default)) : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Languages::setDefault', $language->id)); ?>">Set default</a>
                                    <?php endif ?>
                                    <?php if (empty($language->active)) : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Languages::activate', $language->id)); ?>"><i class="bi bi-toggle-off"></i></a>
                                    <?php else : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Languages::deactivate', $language->id)); ?>"><i class="bi bi-toggle-on"></i></a>
                                    <?php endif ?>
                                    <?php if (! empty($language->variables_count)) : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Languages::delete', $language->id)); ?>" class="modal-alert-link" data-alert-link-text="There are <?= $language->variables_count; ?> variables with the language &quot;<?= $language->title; ?>&quot;. To delete a language uninstall this language from all variables assigned it."><i class="bi bi-trash link-danger"></i></a>
                                    <?php else : ?>
                                        <a href="<?= base_url(route_to('Wagtail\Controllers\Back\Languages::delete', $language->id)); ?>" class="modal-confirm-link" data-confirm-link-text="Are you sure you want to delete language &quot;<?= $language->title; ?>&quot;?"><i class="bi bi-trash link-danger"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p>There are no languages.</p>
    <?php endif ?>
    <div class="overflow-hidden">
        <?= anchor(route_to('Wagtail\Controllers\Back\Languages::add'), 'Add language', ['class' => 'btn btn-primary float-end']); ?>
    </div>
<?= $this->endSection(); ?>