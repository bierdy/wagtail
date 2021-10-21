<?php

namespace Velldoris\Config;

use CodeIgniter\Config\BaseConfig;

class VelldorisApp extends BaseConfig
{
    public $DBGroup = 'velldoris';
    
    public $backRootPath = '/';
    
    public $backSubDomain = 'admin';
    
    public $frontHomeUrlSegment = '/';
}