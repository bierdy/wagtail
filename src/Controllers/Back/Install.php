<?php

namespace Velldoris\Controllers\Back;

use CodeIgniter\Controller;

class Install extends Controller
{
    public function install()
    {
        try {
            $velldoris_app_config = config('Velldoris\\Config\\VelldorisApp');
            $db = \Config\Database::connect($velldoris_app_config->DBGroup);
            $db->getVersion();
        } catch (\Throwable $e) {
            print_r('Velldoris package installation error: ' . $e->getMessage());
            exit;
        }
    
        $migrations = service('Migrations', config('Config\Migrations'), $db);
        $migrations->setNamespace('Velldoris');
    
        try {
            $migrations->latest();
        } catch (\Throwable $e) {
            print_r('Velldoris package installation error: ' . $e->getMessage());
            exit;
        }
    }
}