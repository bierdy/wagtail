<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#header-menu" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="header-menu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link<?= uri_string() == '/' ? ' active' : ''; ?> ps-0" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is('templates*') ? ' active' : ''; ?>" href="<?= route_to('Velldoris\Controllers\Back\Templates::list'); ?>">Templates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is('variables') || url_is('variables/*') ? ' active' : ''; ?>" href="<?= route_to('Velldoris\Controllers\Back\Variables::list'); ?>">Variables</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= url_is('languages*') ? ' active' : ''; ?>" href="<?= route_to('Velldoris\Controllers\Back\Languages::list'); ?>">Languages</a>
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
        </div>
    </nav>
</header>