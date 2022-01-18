<?php

use Wagtail\Filters\Wagtail;

$filters_config = config('Filters');

$filters_config->aliases['wagtail'] = Wagtail::class;
$filters_config->globals['before'][] = 'wagtail';
$filters_config->globals['after'][] = 'wagtail';