<?php

namespace Wagtail\Database\Migrations;

use Wagtail\Database\WagtailMigration;
use CodeIgniter\CLI\CLI;

class Wagtail20220109182323 extends WagtailMigration
{
    protected $tables = [
        'variable_groups' => [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ],
        
        'template_variable_groups' => [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'template_id' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'variable_group_id' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'order' => [
                'type' => 'INT',
                'default' => 1000,
            ],
        ],
    
        'variable_group_variables' => [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'variable_group_id' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'variable_id' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'order' => [
                'type' => 'INT',
                'default' => 1000,
            ],
        ],
    ];
    
    public function up()
    {
        foreach($this->tables as $table_name => $fields)
            if ($this->db->tableExists($table_name))
            {
                CLI::write(CLI::color("Wagtail package migration error: table '{$table_name}' already exist. Delete this table and try migrate again.", 'red') . PHP_EOL);
                exit;
            }
        
        foreach($this->tables as $table_name => $fields)
        {
            $this->forge->addField($fields);
            $this->forge->addPrimaryKey('id');
            $this->forge->createTable($table_name);
        }
    
        $this->forge->dropColumn('template_variables', 'order');
    }
    
    public function down()
    {
    
    }
}