<?php

namespace Wagtail\Config;

$wagtail_app_config = config('WagtailApp');
$routes = service('Routes');

$routes->group($wagtail_app_config->backRootPath, ['namespace' => 'Wagtail\Controllers\Back', 'hostname' => $wagtail_app_config->backDomain], function($routes)
{
    $routes->get('', 'Home::index');
    
    $routes->group('templates', function($routes)
    {
        $routes->get('', 'Templates::list');
        $routes->match(['get', 'post'], 'add', 'Templates::add');
        $routes->match(['get', 'post'], 'edit/(:num)', 'Templates::edit/$1');
        $routes->get('activate/(:num)', 'Templates::activate/$1');
        $routes->get('deactivate/(:num)', 'Templates::deactivate/$1');
        $routes->get('delete/(:num)', 'Templates::delete/$1');
        $routes->get('delete-all', 'Templates::deleteAll');
    });
    
    $routes->group('variables', function($routes)
    {
        $routes->get('', 'Variables::list');
        $routes->match(['get', 'post'], 'add', 'Variables::add');
        $routes->match(['get', 'post'], 'edit/(:num)', 'Variables::edit/$1');
        $routes->get('activate/(:num)', 'Variables::activate/$1');
        $routes->get('deactivate/(:num)', 'Variables::deactivate/$1');
        $routes->get('delete/(:num)', 'Variables::delete/$1');
        $routes->get('delete-all', 'Variables::deleteAll');
        $routes->get('delete-value/(:num)', 'Variables::deleteValue/$1');
    });
    
    $routes->group('resources', function($routes)
    {
        $routes->match(['get', 'post'], 'add/(:num)', 'Resources::add/$1');
        $routes->match(['get', 'post'], 'edit/(:num)', 'Resources::edit/$1');
        $routes->get('activate/(:num)', 'Resources::activate/$1');
        $routes->get('deactivate/(:num)', 'Resources::deactivate/$1');
        $routes->get('delete/(:num)', 'Resources::delete/$1');
        $routes->get('set-template/(:num)/(:num)', 'Resources::setTemplate/$1/$2');
        $routes->get('set-parent/(:num)/(:num)', 'Resources::setParent/$1/$2');
        $routes->get('set-order/(:num)/(:num)', 'Resources::setOrder/$1/$2');
    });
    
    $routes->group('languages', function($routes)
    {
        $routes->get('', 'Languages::list');
        $routes->match(['get', 'post'], 'add', 'Languages::add');
        $routes->match(['get', 'post'], 'edit/(:num)', 'Languages::edit/$1');
        $routes->get('activate/(:num)', 'Languages::activate/$1');
        $routes->get('deactivate/(:num)', 'Languages::deactivate/$1');
        $routes->get('delete/(:num)', 'Languages::delete/$1');
        $routes->get('delete-all', 'Languages::deleteAll');
        $routes->get('set-default/(:num)', 'Languages::setDefault/$1');
    });
    
    $routes->get('get-assets', 'Assets::get');
});