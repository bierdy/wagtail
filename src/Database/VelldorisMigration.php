<?php

namespace Velldoris\Database;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;

abstract class VelldorisMigration extends Migration
{
    public function __construct(? Forge $forge = null)
    {
        $velldoris_app_config = config('Velldoris\\Config\\VelldorisApp');
        
        $this->DBGroup = $velldoris_app_config->DBGroup;
    
        parent::__construct($forge);
    }
}