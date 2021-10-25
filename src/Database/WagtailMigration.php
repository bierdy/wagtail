<?php

namespace Wagtail\Database;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;

abstract class WagtailMigration extends Migration
{
    public function __construct(? Forge $forge = null)
    {
        $wagtail_app_config = config('WagtailApp');
        
        $this->DBGroup = $wagtail_app_config->DBGroup;
    
        parent::__construct($forge);
    }
}