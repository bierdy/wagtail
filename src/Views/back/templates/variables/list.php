<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($variables)) : ?>
        <p class="text-end">Variables count: <?= count($variables); ?></p>
        <p class="text-end"><?= anchor(route_to('Wagtail\Controllers\Back\Variables::deleteAll'), 'Delete all variables', ['class' => 'btn btn-danger modal-confirm-link', 'data-confirm-link-text' => 'Are you sure you want to delete all variables?' . PHP_EOL . 'All values also will be deleted.']); ?></p>
        <div class="table-responsive">
            <table class="table link-secondary">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Name</th>
                        <th>Language</th>
                        <th>Templates count</th>
                        <th>Resources count</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($variables as $variable) { ?>
                        <tr>
                            <td><?= $variable->id; ?></td>
                            <td>
                                <a class="link-secondary text-decoration-none" href="<?= route_to('Wagtail\Controllers\Back\Variables::edit', $variable->id); ?>"><?= $variable->title; ?></a>
                            </td>
                            <td><?= $variable->name; ?></td>
                            <td><?= $variable->language_title; ?></td>
                            <td><?= $variable->templates_count; ?></td>
                            <td><?= $variable->values_count; ?></td>
                            <td><?= date('Y.m.d H:i:s', strtotime($variable->created_at)); ?></td>
                            <td><?= date('Y.m.d H:i:s', strtotime($variable->updated_at)); ?></td>
                            <td>
                                <div class="text-end">
                                    <?php if (empty($variable->active)) : ?>
                                        <a href="<?= route_to('Wagtail\Controllers\Back\Variables::activate', $variable->id); ?>"><i class="bi bi-toggle-off"></i></a>
                                    <?php else : ?>
                                        <a href="<?= route_to('Wagtail\Controllers\Back\Variables::deactivate', $variable->id); ?>"><i class="bi bi-toggle-on"></i></a>
                                    <?php endif ?>
                                    <a href="<?= route_to('Wagtail\Controllers\Back\Variables::delete', $variable->id); ?>" class="modal-confirm-link" data-confirm-link-text="Are you sure you want to delete variable &quot;<?= $variable->title; ?>&quot;?<?= PHP_EOL; ?>The values of this variable for all resources using this variable also will be deleted."><i class="bi bi-trash link-danger"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p>There are no variables.</p>
    <?php endif ?>
    <div class="overflow-hidden">
        <?= anchor(route_to('Wagtail\Controllers\Back\Variables::add'), 'Add variable', ['class' => 'btn btn-primary float-end']); ?>
    </div>
<?= $this->endSection(); ?>