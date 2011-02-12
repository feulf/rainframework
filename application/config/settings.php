<?php
	


//-------------------------------------------------------------
//
//					 Settings
//
//-------------------------------------------------------------

	global $debug;
	$debug = true;	// set true for debug mode on
	define( "TIMEZONE", "Europe/Rome" );

	
	

//-------------------------------------------------------------
//
//					 Application
//
//-------------------------------------------------------------

	define( "DEFAULT_CONTROLLER", "content" );
	define( "DEFAULT_ACTION", "index" );
	define( "PAGE_NOT_FOUND", "not_found" );

	//BASE URL -- experimental way to get the url automatically
	define( "URL", str_replace( basename( $_SERVER['PHP_SELF'] ), '', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']) . "/" ) );




	
	
?>