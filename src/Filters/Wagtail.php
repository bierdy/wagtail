<?php

namespace Wagtail\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Wagtail implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $routerService = service('Router');
        $wagtailService = service('Wagtail');
        $wagtail_admin_config = config('WagtailAdmin');
    
        if (isset($routerService->getMatchedRouteOptions()['namespace']) && mb_strtolower($routerService->getMatchedRouteOptions()['namespace']) === 'wagtail\controllers\back')
            $wagtailService->setInAdminPanel(true);
        
        if ($wagtailService->isInAdminPanel())
        {
            $wagtail_admin_config->headerMenu =
                [
                    'templates' =>
                    [
                        'title' => 'Templates',
                        'link' => base_url(route_to('Wagtail\Controllers\Back\Templates::list')),
                        'active' => url_is(route_to('Wagtail\Controllers\Back\Templates::list') . '*'),
                    ],
                    'variables' =>
                        [
                            'title' => 'Variables',
                            'link' => base_url(route_to('Wagtail\Controllers\Back\Variables::list')),
                            'active' => url_is(route_to('Wagtail\Controllers\Back\Variables::list') . '*'),
                        ],
                    'languages' =>
                        [
                            'title' => 'Languages',
                            'link' => base_url(route_to('Wagtail\Controllers\Back\Languages::list')),
                            'active' => url_is(route_to('Wagtail\Controllers\Back\Languages::list') . '*'),
                        ],
                ];
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    
    }
}