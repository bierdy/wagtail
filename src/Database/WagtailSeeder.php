<?php

namespace Wagtail\Database;

use Config\Database;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Seeder;

class WagtailSeeder extends Seeder
{
    public $seedPath;
    
    public function __construct(Database $config, ? BaseConnection $db = null)
    {
        $wagtail_app_config = config('WagtailApp');
        
        $this->DBGroup = $wagtail_app_config->DBGroup;
        
        parent::__construct($config, $db);
    
        $this->seedPath = __DIR__ . DIRECTORY_SEPARATOR . 'Seeds' . DIRECTORY_SEPARATOR;
    }
}