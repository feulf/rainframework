<?php

	session_start();	

	//----------------------
	// Includes
	//----------------------

	require "inc/functions.php";				// functions	
	require "inc/constants.php";				// constant	
	require "inc/rain.error.php";				// error manager
	require "inc/rain.mysql.class.php";			// mysql
	require "inc/rain.tpl.class.php";			// template
	


	//----------------------
	// Init Database
	//----------------------
	
	// uncomment if you want to use database connection 
	$db = new MySql();
	$db->connect();


	//----------------------
	// Init Template RainTPL
	//----------------------
	raintpl::$tpl_dir = TPL_DIR;
	$tpl = new RainTPL();


	//----------------------
	// Set the timezone
	//----------------------
	if( function_exists( "date_default_timezone_set" ) )
		date_default_timezone_set( TIMEZONE );
		
	//----------------------
	// Set the language
	//----------------------
	define( "LANG_ID", "it" );

	// include the generic vocabulary
	require LANG_DIR . LANG_ID . "/generic.php";

	// set local variables for All but not for numeric and monetary, so that number doesn't have problem in mysql
	setlocale( LC_ALL  ^ LC_NUMERIC ^ LC_MONETARY, explode(",", LOCALE) );

	//----------------------
	// Load Contents
	//---------------------
	/* here your functions to load contents */
	
	// template
	$title = "Rain Framework";
	$content = "easy php framework";

	$tpl->assign( "title", $title );
	$tpl->assign( "content", $content );
	$tpl->assign( "time", time() );
	$tpl->assign( "money", "100" );
	$tpl->draw( "home" );
	

?>