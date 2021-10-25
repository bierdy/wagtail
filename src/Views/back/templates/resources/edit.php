<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
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
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? $resource->title ?? '', ['class' => 'form-control', 'id' => 'title']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Url', 'url', ['class' => 'form-label']); ?>
            <?= form_input('url', $post['url'] ?? $resource->url ?? '', ['class' => 'form-control', 'id' => 'url']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Parent', 'parent_title', ['class' => 'form-label']); ?>
            <?= form_input('parent_title', $parent->title ?? 'Root', ['class' => 'form-control', 'id' => 'parent_title', 'disabled' => 'disabled']); ?>
            <?= form_hidden('parent_id', $parent->id ?? 0); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Template', 'template_title', ['class' => 'form-label']); ?>
            <?= form_input('template_title', $template->title, ['class' => 'form-control', 'id' => 'template_title', 'disabled' => 'disabled']); ?>
            <?= form_hidden('template_id', $template->id); ?>
        </div>
        <?php foreach($variables as $variable) { ?>
            <?php if (! empty($variable->template)) { ?>
                <?= view($variable->template, ['post' => $post, 'variable' => $variable, 'resource' => $resource]); ?>
            <?php } ?>
        <?php } ?>
        <div class="mb-3 overflow-hidden">
            <?= form_submit('submit', 'Update', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>