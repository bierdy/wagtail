<?php

if (! function_exists('print_r2'))
{
    function print_r2($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

if (! function_exists('buildResourcesTree'))
{
    function buildResourcesTree(array $branches, int $tree_level = 0) : string
    {
        $tree = '';
        $tree_level++;
        
        if (! empty($branches))
        {
            $cookies_resources_tree = getWagtailCookie('resources_tree');
            
            if (! is_null($cookies_resources_tree) && ! is_null($cookies_resources_tree = json_decode($cookies_resources_tree)) && is_array($cookies_resources_tree->open_branches))
                $open_branches = $cookies_resources_tree->open_branches;
            else
                $open_branches = [];
    
            $uri_string = trim(uri_string(true), '/');
                
            foreach($branches as $branch)
            {
                $is_open_branch = in_array($branch->id, $open_branches);
                $is_active_branch = $uri_string === trim(route_to('Wagtail\Controllers\Back\Resources::edit', $branch->id), '/');
                $is_childs_exist = ! empty($branch->childs);
                $childs = $is_childs_exist ? $branch->childs : [];
    
                $edit_link = "<a class='title link-secondary' href='" . base_url(route_to("Wagtail\Controllers\Back\Resources::edit", $branch->id)) . "' draggable='true'>";
                    $edit_link .= "<i class='template-icon {$branch->template_icon} link-secondary'></i>";
                    $edit_link .= "{$branch->title}";
                $edit_link .= "</a>";
                
                $add_link = "<li><a class='dropdown-item' href='" . base_url(route_to('Wagtail\Controllers\Back\Resources::add', $branch->id)) . "'>Add resource</a></li>";
    
                $set_template_links = '';
                if (! empty($branch->available_templates))
                {
                    $set_template_links .= "<li class='btn-group dropend w-100'>";
                        $set_template_links .= "<button class='dropdown-item' type='button' data-bs-toggle='dropdown'>Set template</button>";
                        $set_template_links .= "<ul class='dropdown-menu'>";
                        foreach($branch->available_templates as $available_template)
                        {
                            $set_template_links .= "<li>";
                                $set_template_links .= "<a class='dropdown-item modal-confirm-link' href='" . base_url(route_to('Wagtail\Controllers\Back\Resources::setTemplate', $branch->id, $available_template->id)) . "' data-confirm-link-text='Are you sure you want to set &quot;" . ucfirst(mb_strtolower($available_template->title)) . "&quot; template?" . PHP_EOL . "All resource variables will be deleted!'>";
                                    if (! empty($available_template->icon))
                                        $set_template_links .= "<i class='{$available_template->icon} link-secondary me-1'></i>";
                                    $set_template_links .= $available_template->title;
                                $set_template_links .= "</a>";
                            $set_template_links .= "</li>";
                        }
                        $set_template_links .= "</ul>";
                    $set_template_links .= "</li>";
                }
    
                $activate_link = '';
                $deactivate_link = '';
                if (empty($branch->active))
                    $activate_link = "<li><a class='dropdown-item' href='" . base_url(route_to('Wagtail\Controllers\Back\Resources::activate', $branch->id)) . "'>Activate</a></li>";
                else
                    $deactivate_link = "<li><a class='dropdown-item' href='" . base_url(route_to('Wagtail\Controllers\Back\Resources::deactivate', $branch->id)) . "'>Deactivate</a></li>";
                
                $delete_link = "<li><a class='dropdown-item link-danger modal-confirm-link' href='" . base_url(route_to('Wagtail\Controllers\Back\Resources::delete', $branch->id)) . "' data-confirm-link-text='Are you sure you want to delete " . ($branch->template_unique ? "&quot;" . $branch->title . "&quot; page" : mb_strtolower($branch->template_title) . " &quot;" . $branch->title . "&quot;") . "?" . ($is_childs_exist ? PHP_EOL . 'All child resources will also be deleted!' : '') . "'>Delete</a></li>";
    
                $open_branch_class = $is_open_branch ? ' open' : '';
                $open_tree_class = $is_open_branch ? ' show' : '';
                $active_branch_class = $is_active_branch ? ' active' : '';
                $childs_exist_class = $is_childs_exist ? ' childs-exist' : '';
                $deactivated_branch_class = empty($branch->active) ? ' deactivated' : '';
                
                $tree .= "<li class='branch{$open_branch_class}{$active_branch_class}{$childs_exist_class}{$deactivated_branch_class}' data-branch-id='{$branch->id}'>";
                    $tree .= "<div class='content'>";
                        $tree .= "<div class='main'>";
                            $tree .= "<i class='arrow bi bi-chevron-right link-secondary' data-bs-target='[data-tree-id=\"{$branch->id}\"]'></i>";
                            $tree .= $edit_link;
                        $tree .= "</div>";
                        $tree .= "<div class='menu'>";
                            $tree .= "<i class='button bi bi-list link-secondary' data-bs-toggle='dropdown'></i>";
                            $tree .= "<ul class='dropdown-menu'>";
                                $tree .= $add_link;
                                $tree .= $set_template_links;
                                $tree .= $activate_link;
                                $tree .= $deactivate_link;
                                $tree .= "<li><hr class='dropdown-divider'></li>";
                                $tree .= $delete_link;
                            $tree .= "</ul>";
                        $tree .= "</div>";
                    $tree .= "</div>";
    
                    $tree .= "<ul class='tree{$open_tree_class}' data-tree-id='{$branch->id}' data-tree-level='{$tree_level}'>";
                        $tree .= buildResourcesTree($childs, $tree_level);
                    $tree .= "</ul>";
                    
                $tree .= "</li>";
            }
        }
    
        return $tree;
    }
}

if (! function_exists('setWagtailCookie'))
{
    function setWagtailCookie(string $name = '', string $value = '', array $options = [])
    {
        if (empty($name))
            return;
        
        $response = service('Response');
        $wagtail_cookie_config = config('WagtailCookie');
        $options = array_merge((array) $wagtail_cookie_config, $options);
        
        $cookie = [
            'name' => $name,
            'value' => $value,
            'expire' => $options['expires'],
            'domain' => $options['domain'],
            'path' => $options['path'],
            'prefix' => $options['prefix'],
            'secure' => $options['secure'],
            'httponly' => $options['httponly'],
            'samesite' => $options['samesite'],
        ];
        
        $response->setCookie($cookie);
    }
}

if (! function_exists('getWagtailCookie'))
{
    function getWagtailCookie(string $name = '', bool $delete = false) : ? string
    {
        if (empty($name))
            return '';
        
        $request = service('Request');
        $wagtail_cookie_config = config('WagtailCookie');
        
        $cookie = $request->getCookie($wagtail_cookie_config->prefix . $name);
        
        if (! is_null($cookie) && $delete)
            deleteWagtailCookie($name);
        
        return $cookie;
    }
}

if (! function_exists('deleteWagtailCookie'))
{
    function deleteWagtailCookie(string $name = '')
    {
        $response = service('Response');
        $wagtail_cookie_config = config('WagtailCookie');
        
        $response->deleteCookie($name, $wagtail_cookie_config->domain, $wagtail_cookie_config->path, $wagtail_cookie_config->prefix);
    }
}

if (! function_exists('getFrontRootUrl'))
{
    function getFrontRootUrl() : string
    {
        $wagtail_app_config = config('WagtailApp');
        
        $https = ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
        $server_port = ! empty($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null;
    
        $scheme = $https || ($server_port == 443) ? 'https' : 'http';
        
        if (! empty($wagtail_app_config->frontDomain))
            $front_root_url = $scheme . '://' . $wagtail_app_config->frontDomain . '/';
        else
            $front_root_url = site_url();
        
        return $front_root_url;
    }
}

if (! function_exists('getWagtailComposerPackage'))
{
    function getWagtailComposerPackage() : array
    {
        $composer_lock_file = file_get_contents(ROOTPATH . 'composer.lock');
        
        if ($composer_lock_file === false)
            return [];
        
        $composer_lock_array = json_decode($composer_lock_file, true);
    
        if (is_null($composer_lock_array))
            return [];
    
        $composer_lock_packages = array_combine(array_column($composer_lock_array['packages'], 'name'), $composer_lock_array['packages']);
        
        return $composer_lock_packages['bierdy/wagtail'] ?? [];
    }
}

if (! function_exists('setWagtailAdminConfigHeaderMenu'))
{
    function setWagtailAdminConfigHeaderMenu() : void
    {
        $wagtail_admin_config = config('WagtailAdmin');
    
        $wagtail_admin_config->headerMenu =
            [
                'templates' =>
                    [
                        'title' => 'Templates',
                        'link' => base_url(route_to('Wagtail\Controllers\Back\Templates::list')),
                        'active' => url_is(route_to('Wagtail\Controllers\Back\Templates::list') . '*'),
                    ],
                'variables' =>
                    [
                        'title' => 'Variables',
                        'link' => base_url(route_to('Wagtail\Controllers\Back\Variables::list')),
                        'active' => url_is(route_to('Wagtail\Controllers\Back\Variables::list') . '*'),
                    ],
                'languages' =>
                    [
                        'title' => 'Languages',
                        'link' => base_url(route_to('Wagtail\Controllers\Back\Languages::list')),
                        'active' => url_is(route_to('Wagtail\Controllers\Back\Languages::list') . '*'),
                    ],
            ];
    }
}