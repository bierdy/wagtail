<?php

namespace Velldoris\Config;

use CodeIgniter\Config\BaseConfig;

class VelldorisApp extends BaseConfig
{
    public $DBGroup = 'velldoris';
    
    public $backRootPath = 'admin';
    
    public $frontRootPath = '/';
    
    public $backDomain = '';
    
    public $frontDomain = '';
}