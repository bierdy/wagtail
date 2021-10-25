<?= $this->extend('Wagtail\Views\back\templates\default'); ?>

<?= $this->section('template'); ?>
    <?php if (! empty($errors)) : ?>
        <div class="alert alert-danger">
            <?= $message; ?>
            <?php foreach ($errors as $error) : ?>
                <div><?= $error; ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
    <?= form_open(); ?>
        <div class="mb-3">
            <?= form_label('Title', 'title', ['class' => 'form-label']); ?>
            <?= form_input('title', $post['title'] ?? '', ['class' => 'form-control' , 'id' => 'title']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Code', 'code', ['class' => 'form-label']); ?>
            <?= form_input('code', $post['code'] ?? '', ['class' => 'form-control' , 'id' => 'code']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Icon', 'icon', ['class' => 'form-label']); ?>
            <?= form_input('icon', $post['icon'] ?? '', ['class' => 'form-control' , 'id' => 'icon']); ?>
        </div>
        <div class="mb-3">
            <?= form_label('Order', 'order', ['class' => 'form-label']); ?>
            <?= form_input('order', $post['order'] ?? 1000, ['class' => 'form-control' , 'id' => 'order']); ?>
        </div>
        <div class="mb-3 overflow-hidden">
            <?= anchor(route_to('Wagtail\Controllers\Back\Languages::list'), 'Back', ['class' => 'btn btn-secondary float-start']); ?>
            <?= form_submit('submit', 'Add', ['class' => 'btn btn-primary float-end']); ?>
        </div>
    <?= form_close(); ?>
<?= $this->endSection(); ?>