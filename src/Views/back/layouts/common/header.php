<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#header-menu" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="header-menu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <a class="navbar-brand" href="<?= base_url(route_to('Wagtail\Controllers\Back\Home::index')); ?>">
                        <img src="<?= base_url(route_to('Wagtail\Controllers\Back\Assets::get')); ?>?path=Wagtail\\Views\\back\\assets\\img\\&name=favicon&ext=png" alt="Wagtail" width="24" height="24">
                    </a>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is(route_to('Wagtail\Controllers\Back\Home::index')) ? ' active text-decoration-underline' : ''; ?> ps-0" href="<?= base_url(route_to('Wagtail\Controllers\Back\Home::index')); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is(route_to('Wagtail\Controllers\Back\Templates::list') . '*') ? ' active text-decoration-underline' : ''; ?>" href="<?= base_url(route_to('Wagtail\Controllers\Back\Templates::list')); ?>">Templates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is(route_to('Wagtail\Controllers\Back\Variables::list') . '*') ? ' active text-decoration-underline' : ''; ?>" href="<?= base_url(route_to('Wagtail\Controllers\Back\Variables::list')); ?>">Variables</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is(route_to('Wagtail\Controllers\Back\Languages::list') . '*') ? ' active text-decoration-underline' : ''; ?>" href="<?= base_url(route_to('Wagtail\Controllers\Back\Languages::list')); ?>">Languages</a>
                    </li>
                    
                    <!--
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is('offer-products*') ? ' active' : ''; ?>" href="/offer-products">Offer products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is('products*') ? ' active' : ''; ?>" href="/products">Products</a>
                    </li>
                    -->
                    
                </ul>
            </div>
            <a class="" href="/">Front</a>
        </div>
    </nav>
</header>