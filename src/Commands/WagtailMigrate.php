<?php

namespace Wagtail\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class WagtailMigrate extends BaseCommand
{
    protected $group = 'Wagtail';
    protected $name = 'wagtail:migrate';
    protected $description = 'Runs all new Wagtail migrations against the database.';
    
    public function run(array $params)
    {
        try {
            $wagtail_app_config = config('WagtailApp');
            $db = \Config\Database::connect($wagtail_app_config->DBGroup);
            $db->getVersion();
        } catch (\Throwable $e) {
            CLI::write(CLI::color('Wagtail migration error: ' . $e->getMessage(), 'red') . PHP_EOL);
            exit;
        }
        
        $migrations = service('Migrations', config('Config\Migrations'), $db);
        $migrations->setNamespace('Wagtail');
        
        try {
            $migrations->latest();
        } catch (\Throwable $e) {
            CLI::write(CLI::color('Wagtail migration error: ' . $e->getMessage(), 'red') . PHP_EOL);
            exit;
        }
    }
}