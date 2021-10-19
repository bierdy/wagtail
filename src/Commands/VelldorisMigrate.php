<?php

namespace Velldoris\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VelldorisMigrate extends BaseCommand
{
    protected $group = 'Velldoris';
    protected $name = 'velldoris:migrate';
    protected $description = 'Runs all new Velldoris migrations against the database.';
    
    public function run(array $params)
    {
        try {
            $velldoris_app_config = config('Velldoris\\Config\\VelldorisApp');
            $db = \Config\Database::connect($velldoris_app_config->DBGroup);
            $db->getVersion();
        } catch (\Throwable $e) {
            CLI::write(CLI::color('Velldoris package installation error: ' . $e->getMessage(), 'red') . PHP_EOL);
            exit;
        }
        
        $migrations = service('Migrations', config('Config\Migrations'), $db);
        $migrations->setNamespace('Velldoris');
        
        try {
            $migrations->latest();
        } catch (\Throwable $e) {
            CLI::write(CLI::color('Velldoris package installation error: ' . $e->getMessage(), 'red') . PHP_EOL);
            exit;
        }
    }
}