<?php

    #--------------------------------
    # Set the route configuration and the default
    # controller_dir, controller adn action
    #--------------------------------

    $route['controller_dir_in_route'] = false;
    $route['default_controller_dir'] = '';
    $route['default_controller'] = 'content';
    $route['default_action'] = 'index';

    #--------------------------------
    # In Rain Framework is possible to
    # configure few default route
    #--------------------------------

    /**
     * Route configuration examples:
     *
     * Get any value (eg. index.php/user/rain/  =>  index.php/user/profile/rain/ )
     * $route['user/(:any)/']   = 'user/profile/$1';
     *
     * Get any value (eg. index.php/user/10/  =>  index.php/user/edit/10/ )
     * $route['user/(:num)/']   = 'user/edit/$1';
     *
     * Convert a static URI (eg. index.php/blog/php/  =>  index.php/blog/category/php/ )
     * $route['blog/php/']  = 'blog/category/php/';
     *
     */




?>