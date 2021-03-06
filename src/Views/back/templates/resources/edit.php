<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <div class="row justify-content-between mt-0 mb-4">
        <div class="col-auto">
            <div>Parent: <?= $parent->title ?? 'Root'; ?></div>
        </div>
        <div class="col-auto">
            <div>Template: <?= $template->title; ?></div>
        </div>
    </div>
    <?php if (! empty($errors)) : ?>
        <div class="alert alert-danger">
            <?= $message; ?>
            <?php foreach ($errors as $error) : ?>
                <div><?= $error; ?></div>
            <?php endforeach ?>
        </div>
    <?php elseif (! empty($message)) : ?>
        <div class="alert alert-success">
            <?= $message; ?>
        </div>
    <?php endif ?>
    <?= form_open_multipart(); ?>
        <?= form_hidden('id', $resource->id ?? 0); ?>
        <?= form_hidden('parent_id', $parent->id ?? 0); ?>
        <?= form_hidden('template_id', $template->id); ?>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
                    <?= form_input('title', $post['title'] ?? $resource->title ?? '', ['class' => 'form-control', 'id' => 'title']); ?>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <?= form_label('Url', 'url', ['class' => 'form-label']); ?>
                    <?= form_input('url', $post['url'] ?? $resource->url ?? '', ['class' => 'form-control', 'id' => 'url']); ?>
                </div>
            </div>
        </div>
        <?php if (! empty($template_variable_groups)) : ?>
            <ul class="nav nav-tabs mt-4 mb-3">
                <?php foreach($template_variable_groups as $key => $template_variable_group) { ?>
                    <li class="nav-item">
                        <button class="nav-link<?= empty($key) ? ' active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#variable-group-id-<?= $template_variable_group->id; ?>" type="button"><?= $template_variable_group->title; ?></button>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <?php foreach($template_variable_groups as $key => $template_variable_group) { ?>
                    <div class="tab-pane fade<?= empty($key) ? ' show active' : ''; ?>" id="variable-group-id-<?= $template_variable_group->id; ?>">
                        <?php foreach($variable_group_variables as $variable_group_variable) { ?>
                            <?php if ($variable_group_variable->variable_group_id !== $template_variable_group->id) continue; ?>
                            <?php foreach($variables as $variable) { ?>
                                <?php if ($variable->id === $variable_group_variable->variable_id && ! empty($variable->template)) { ?>
                                    <?= view($variable->template, ['post' => $post, 'variable' => $variable, 'resource' => $resource]); ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php endif; ?>
        <div class="mb-3 overflow-hidden">
            <?= form_submit('submit', 'Update', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>