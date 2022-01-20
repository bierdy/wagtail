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
    
        if (isset($routerService->getMatchedRouteOptions()['namespace']) && mb_strtolower($routerService->getMatchedRouteOptions()['namespace']) === 'wagtail\controllers\back')
            $wagtailService->setInAdminPanel(true);
        
        if ($wagtailService->isInAdminPanel())
            setWagtailAdminConfigHeaderMenu();
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    
    }
}