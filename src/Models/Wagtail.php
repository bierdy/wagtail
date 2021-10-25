<?php

namespace Wagtail\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class Wagtail extends Model
{
    public function __construct(? ConnectionInterface &$db = null, ? ValidationInterface $validation = null)
    {
        $wagtail_app_config = config('WagtailApp');
        
        $this->DBGroup = $wagtail_app_config->DBGroup;
        
        parent::__construct($db, $validation);
    }
}