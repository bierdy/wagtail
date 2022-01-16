<?= doctype(); ?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
        <meta name="format-detection" content="telephone=no">
        <title><?= $title; ?></title>
        <link href="<?= base_url(route_to('Wagtail\Controllers\Back\Assets::get')); ?>?path=Wagtail\\Views\\back\\assets\\img\\&name=favicon&ext=png" rel="shortcut icon" type="image/png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" type="text/css">
        <link href="<?= base_url(route_to('Wagtail\Controllers\Back\Assets::get')); ?>?path=Wagtail\\Views\\back\\assets\\css\\&name=styles&ext=css" rel="stylesheet" type="text/css">
        <script>
            window.app_config = <?= $app_config; ?>;
            window.wagtail_app_config = <?= $wagtail_app_config; ?>;
            window.wagtail_cookie_config = <?= $wagtail_cookie_config; ?>;
        </script>
    </head>
    <body>
        <?= view('Wagtail\Views\back\layouts\common\header'); ?>
        <main class="main mt-4">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-auto">
                        <div class="left-sidebar" style="<?= ! is_null(getWagtailCookie('left_sidebar_width')) ? 'width: ' . getWagtailCookie('left_sidebar_width') . 'px' : ''; ?>">
                            <?= view('Wagtail\Views\back\layouts\common\left_sidebar'); ?>
                        </div>
                    </div>
                    <div class="col-auto d-none d-md-block px-1">
                        <div class="resizer px-1"></div>
                    </div>
                    <div class="col-12 col-md">
                        <h1><?= $title; ?></h1>
                        <?= $this->renderSection('template'); ?>
                    </div>
                </div>
            </div>
        </main>
        <?= view('Wagtail\Views\back\layouts\common\footer'); ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/29.2.0/classic/ckeditor.js"></script>
        <script src="<?= base_url(route_to('Wagtail\Controllers\Back\Assets::get')); ?>?path=Wagtail\\Views\\back\\assets\\js\\&name=app&ext=js"></script>
    </body>
</html>