<?php

$filters_config = config('Filters');

$filters_config->aliases['wagtail'] = Wagtail\Filters\Wagtail::class;
array_unshift($filters_config->globals['before'], 'wagtail');
array_unshift($filters_config->globals['after'], 'wagtail');