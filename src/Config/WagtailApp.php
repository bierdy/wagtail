<?php

namespace Wagtail\Config;

use CodeIgniter\Config\BaseConfig;

class WagtailApp extends BaseConfig
{
    public $DBGroup = 'wagtail';
    
    public $backRootPath = 'admin';
    
    public $frontRootPath = '/';
    
    public $backDomain = '';
    
    public $frontDomain = '';
    
    public $resourceUrlSeparator = '-';
    
    public $resourceUrlCopyPostfix = 'copy';
    
    public $resourceUrlEmpty = 'empty';
}