<?php

namespace Velldoris\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class Velldoris extends Model
{
    public function __construct(? ConnectionInterface &$db = null, ? ValidationInterface $validation = null)
    {
        $velldoris_app_config = config('Velldoris\\Config\\VelldorisApp');
        
        $this->DBGroup = $velldoris_app_config->DBGroup;
        
        parent::__construct($db, $validation);
    }
}