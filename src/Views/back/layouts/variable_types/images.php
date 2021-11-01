<div class="mb-3">
    <?= form_label($variable->title, $variable->name, ['class' => 'form-label']); ?>
    <?php $options = json_decode($variable->options); ?>
    <?php $images = ! empty($variable->values) ? $variable->values : (! empty($variable->value) ? [$variable->value] : []); ?>
    <?php if (! empty($images)) { ?>
        <?php $image_path = '/' . trim(str_replace('{resource_id}', $resource->id, $options->path), '/') . '/'; ?>
        <?php $image_path = site_url($image_path); ?>
        <?php $image_height = $options->admin_image_height; ?>
        <div class="input-images">
            <?php foreach ($images as $image) { ?>
                <?php
                $image_src = $image_path . $image->value;
                $delete_link = base_url(route_to('Wagtail\Controllers\Back\Variables::deleteValue', $image->id));
                ?>
                <div class="input-image">
                    <img class="image" src="<?= $image_src; ?>" height="<?= $image_height; ?>">
                    <a class="delete-link link-danger modal-confirm-link" href="<?= $delete_link; ?>" data-confirm-link-text="Are you sure you want to delete image?">
                        <i class='bi bi-x-circle'></i>
                    </a>
                    <?= form_input($variable->name . '_orders' . "[{$image->id}]", $post[$variable->name . '_orders'][$image->id] ?? $image->order ?? 1000, ['class' => 'form-control order' , 'id' => $variable->name]); ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?= form_upload($variable->name . '[]', '', ['class' => 'form-control', 'id' => $variable->name, 'multiple' => 'multiple']); ?>
    <?= form_hidden($variable->name); ?>
</div>