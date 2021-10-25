<?php

namespace Wagtail\Database\Migrations;

use Wagtail\Database\WagtailMigration;
use CodeIgniter\CLI\CLI;

class Wagtail20211012094343 extends WagtailMigration
{
    protected $tables = [
        'resources' => [
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type' => 'BIGINT',
                'default' => 0,
            ],
            'template_id' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'title' => [
                'type' => 'TEXT',
            ],
            'url' => [
                'type' => 'TEXT',
            ],
            'order' => [
                'type' => 'INT',
                'default' => 1000,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        
        'templates' => [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'TEXT',
            ],
            'icon' => [
                'type' => 'TEXT',
            ],
            'class_method' => [
                'type' => 'TEXT',
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'unique' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        
        'template_variables' => [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'template_id' => [
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
        
        'variables' => [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'TEXT',
            ],
            'name' => [
                'type' => 'TEXT',
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'class' => [
                'type' => 'TEXT',
            ],
            'options' => [
                'type' => 'TEXT',
            ],
            'template' => [
                'type' => 'TEXT',
            ],
            'validation_rules' => [
                'type' => 'TEXT',
            ],
            'language_id' => [
                'type' => 'INT',
                'default' => 0,
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
        
        'variable_values' => [
            'id' => [
                'type' => 'BIGINT',
                'auto_increment' => true,
            ],
            'resource_id' => [
                'type' => 'BIGINT',
                'default' => 0,
            ],
            'variable_id' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'value' => [
                'type' => 'LONGTEXT',
            ],
            'order' => [
                'type' => 'INT',
                'default' => 1000,
            ],
        ],
        
        'languages' => [
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'icon' => [
                'type' => 'TEXT',
            ],
            'default' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'order' => [
                'type' => 'INT',
                'default' => 1000,
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
    ];
    
    public function up()
    {
        foreach($this->tables as $table_name => $fields)
            if ($this->db->tableExists($table_name))
            {
                CLI::write(CLI::color("Wagtail package installation error: table '{$table_name}' already exist. Delete this table and try install again.", 'red') . PHP_EOL);
                exit;
            }
        
        foreach($this->tables as $table_name => $fields)
        {
            $this->forge->addField($fields);
            $this->forge->addPrimaryKey('id');
            $this->forge->createTable($table_name);
        }
    }
    
    public function down()
    {
    
    }
}