<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand order-1 pt-0 me-4 me-lg-2" href="<?= base_url(route_to('Wagtail\Controllers\Back\Home::index')); ?>">
                <img src="<?= base_url(route_to('Wagtail\Controllers\Back\Assets::get')); ?>?path=Wagtail\\Views\\back\\assets\\img\\&name=favicon&ext=png" alt="Wagtail" width="24" height="24">
            </a>
            <button class="navbar-toggler order-2" type="button" data-bs-toggle="collapse" data-bs-target="#header-menu" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse order-4 order-lg-3" id="header-menu">
                <ul class="navbar-nav me-auto mb-0 pt-3 pb-2 py-lg-0 text-center">
                    <?php if (! empty($back_header_menu)) : ?>
                        <?php foreach($back_header_menu as $back_header_menu_item) : ?>
                            <li class="nav-item">
                                <a class="nav-link<?= $back_header_menu_item['active'] ? ' active text-decoration-underline' : ''; ?>" href="<?= $back_header_menu_item['link']; ?>"><?= $back_header_menu_item['title']; ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <a class="navbar-brand order-3 order-lg-4 m-0" href="<?= $front_root_url; ?>" target="_blank">Front</a>
        </div>
    </nav>
</header>