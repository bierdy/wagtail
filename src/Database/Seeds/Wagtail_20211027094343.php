<?php

namespace Wagtail\Database\Seeds;

use Wagtail\Database\WagtailSeeder;
use CodeIgniter\CLI\CLI;

class Wagtail_20211027094343 extends WagtailSeeder
{
    protected $variable_rows = [
        [
            'title' => 'Meta title',
            'name' => 'meta_title',
            'class' => '\Wagtail\Variables\Text',
            'options' => '',
            'template' => 'Wagtail\Views\back\layouts\variable_types\text_input',
            'validation_rules' => '',
        ],
        [
            'title' => 'Meta description',
            'name' => 'meta_description',
            'class' => '\Wagtail\Variables\Text',
            'options' => '',
            'template' => 'Wagtail\Views\back\layouts\variable_types\text_input',
            'validation_rules' => '',
        ],
        [
            'title' => 'Meta keywords',
            'name' => 'meta_keywords',
            'class' => '\Wagtail\Variables\Text',
            'options' => '',
            'template' => 'Wagtail\Views\back\layouts\variable_types\text_input',
            'validation_rules' => '',
        ],
        [
            'title' => 'Description',
            'name' => 'description',
            'class' => '\Wagtail\Variables\Text',
            'options' => '',
            'template' => 'Wagtail\Views\back\layouts\variable_types\ckeditor',
            'validation_rules' => '',
        ],
        [
            'title' => 'Single image',
            'name' => 'single_image',
            'class' => '\Wagtail\Variables\Image',
            'options' => <<<EOT
                {
                  "path": "/assets/img/single-images/{resource_id}/single-image",
                  "admin_image_height": 80,
                  "thumbs":
                  [
                    {
                      "postfix": "",
                      "methods":
                      [
                        {
                          "name": "setImageFormat",
                          "options":
                          [
                            "webp"
                          ]
                        }
                      ]
                    },
                    {
                      "postfix": "_100x100",
                      "methods":
                      [
                        {
                          "name": "resizeImage",
                          "options":
                          [
                            100,
                            100,
                            22,
                            1
                          ]
                        },
                        {
                          "name": "setImageFormat",
                          "options":
                          [
                            "webp"
                          ]
                        }
                      ]
                    }
                  ]
                }
                EOT,
            'template' => 'Wagtail\Views\back\layouts\variable_types\image',
            'validation_rules' => <<<EOT
                {
                  "rules":
                  {
                    "single_image": "max_size[single_image,4096]"
                  }
                }
                EOT,
        ],
        [
            'title' => 'Multiple image',
            'name' => 'multiple_image',
            'class' => '\Wagtail\Variables\Images',
            'options' => <<<EOT
                {
                  "path": "/assets/img/multiple-images/{resource_id}/multiple-image",
                  "admin_image_height": 80,
                  "thumbs":
                  [
                    {
                      "postfix": "",
                      "methods":
                      [
                        {
                          "name": "setImageFormat",
                          "options":
                          [
                            "webp"
                          ]
                        }
                      ]
                    }
                  ]
                }
                EOT,
            'template' => 'Wagtail\Views\back\layouts\variable_types\images',
            'validation_rules' => <<<EOT
                {
                  "rules":
                  {
                    "multiple_image": "max_size[multiple_image,4096]",
                    "multiple_image_orders": "integer"
                  }
                }
                EOT,
        ],
    ];
    
    public function run()
    {
        $variableModel = model('Variable');
        $builder = $variableModel->db->table($variableModel->table);
        $date_now = date('Y-m-d H:i:s');
        
        if (! $this->db->tableExists($variableModel->table))
        {
            CLI::write(CLI::color("Wagtail package installation error: table '{$variableModel->table}' does not exist. Run Wagtail installation migration first and try install seed again.", 'red') . PHP_EOL);
            exit;
        }
    
        foreach($this->variable_rows as $variable_row)
        {
            $table_variable_row = $variableModel->where('name', $variable_row['name'])->first();
            
            if (! is_null($table_variable_row))
                $builder
                    ->where($variableModel->primaryKey, $table_variable_row->{$variableModel->primaryKey})
                    ->update(array_merge($variable_row, ['updated_at' => $date_now]));
            else
                $builder
                    ->insert(array_merge($variable_row, ['created_at' => $date_now, 'updated_at' => $date_now]));
        }
    }
}