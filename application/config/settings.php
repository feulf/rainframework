<?php

	global $debug;
	$debug = true;	// set true for debug mode on

	global $settings;

	$settings['timezone'] 			= "Europe/Rome";    // server timezone
	$settings['default_controller_dir']   = "content";          // default controller directory, only if use Loader::$controller_dir_in_route=true
	$settings['default_controller'] 	= "content";        // default controller
	$settings['default_action'] 		= "index";          // default controller action
	$settings['page_not_found'] 		= "not_found";      // page not found
	$settings['view_class']                 = "Raintpl";
	$settings['form_class']                 = "Rain";
	$settings['user_class']                 = "Rain";
        $settings['group_class']                = "Rain";
        $settings['url'] = str_replace( basename( $_SERVER['PHP_SELF'] ), '', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) . "/" );
	define( "URL", $settings['url'] ); // base url

?>