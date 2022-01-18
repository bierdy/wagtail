<?php

namespace Wagtail\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Wagtail implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
    
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    
    }
}