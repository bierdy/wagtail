<div class="mb-3">
    <?= form_label($variable->title, $variable->name, ['class' => 'form-label']); ?>
    <?php $options = json_decode($variable->options); ?>
    <?php if (! empty($variable->value)) { ?>
        <?php
        $image_path = '/' . trim(str_replace('{resource_id}', $resource->id, $options->path), '/') . '/';
        $image_src = $image_path . $variable->value->value;
        $delete_link = route_to('Wagtail\Controllers\Back\Variables::deleteValue', $variable->value->id);
        $image_height = $options->admin_image_height;
        ?>
        <div class="input-images">
            <div class="input-image">
                <img class="image" src="<?= $image_src; ?>" height="<?= $image_height; ?>">
                <a class="delete-link link-danger modal-confirm-link" href="<?= $delete_link; ?>" data-confirm-link-text="Are you sure you want to delete image?">
                    <i class='bi bi-x-circle'></i>
                </a>
            </div>
        </div>
    <?php } ?>
    <?= form_upload($variable->name, '', ['class' => 'form-control', 'id' => $variable->name]); ?>
    <?= form_hidden($variable->name); ?>
</div>