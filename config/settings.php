<?php

	global $debug;
	$debug = true;	// set true for debug mode on

	global $settings;

    $settings['timezone'] 			= "America/New_York";    // server timezone
    $settings['url'] = str_replace( basename( $_SERVER['PHP_SELF'] ), '', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) . "/" );
    $settings['memory_limit']       = "64M"; // Memory limit for exceptional functions (example image resize)
	define( "URL", $settings['url'] ); // base url

?>