<?php

namespace Wagtail\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Wagtail\Database\WagtailSeeder;
use CodeIgniter\Autoloader\Autoloader;
use CodeIgniter\Autoloader\FileLocator;

class WagtailSeed extends BaseCommand
{
    protected $group = 'Wagtail';
    protected $name = 'wagtail:seed';
    protected $description = 'Runs the Wagtail seeder to populate known data into the database.';
    
    public function run(array $params)
    {
        try {
            $wagtail_app_config = config('WagtailApp');
            $db = \Config\Database::connect($wagtail_app_config->DBGroup);
            $db->getVersion();
        } catch (\Throwable $e) {
            CLI::write(CLI::color('Wagtail seed error: ' . $e->getMessage(), 'red') . PHP_EOL);
            exit;
        }
        
        $seeder = new WagtailSeeder(config('Database'), $db);
        $fileLocator = new FileLocator(new Autoloader());
        $seeds_files = get_filenames($seeder->seedPath, true);
        $seeds_files = array_map(function($seed_file) use($fileLocator) {
            return $fileLocator->getClassname($seed_file);
        }, $seeds_files);
        
        foreach($seeds_files as $seed_file)
            try {
                $seeder->call($seed_file);
            } catch (\Throwable $e) {
                CLI::write(CLI::color('Wagtail seed error: ' . $e->getMessage(), 'red') . PHP_EOL);
                exit;
            }
    }
}