<?php

	// check that we are using rain framework
	define( "RAIN", true );
        define( "RAINFRAMEWORK_VERSION", "Rain Framework 2.2" );




//-------------------------------------------------------------
//
//					 Controller
//
//-------------------------------------------------------------

	define( "CONTROLLER_EXTENSION", 	"php" );
	define( "CONTROLLER_CLASS_NAME", 	"_Controller" );
	define( "AJAX_CONTROLLER_EXTENSION", 	"Ajax.php" );
	define( "AJAX_CONTROLLER_CLASS_NAME", 	"_Ajax_Controller" );



//-------------------------------------------------------------
//
//					 User Info
//
//-------------------------------------------------------------

	// get user IP
	$IP = getenv( "HTTP_X_FORWARDED_FOR" ) ? getenv( "HTTP_X_FORWARDED_FOR" ) : getenv( "REMOTE_ADDR" ); if( !preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $IP ) ) $IP = null;
	// browser calculation
	$known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko'); preg_match( '#(' . join('|', $known) . ')[/ ]+([0-9]+(?:\.[0-9]+)?)#', strtolower($_SERVER['HTTP_USER_AGENT']), $br ); preg_match_all( '#\((.*?);#', $_SERVER['HTTP_USER_AGENT'], $os ); if( isset( $br[1][1] ) )	$browser = $br[1][1]; else $browser = null;  if( isset( $br[2][1] ) ) $version = $br[2][1]; else $version = null;

	define( "IP", $IP );
	define( "BROWSER_LANG_ID", substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) );
	define( "BROWSER", $browser );
	define( "BROWSER_VERSION", $version );
	define( "BROWSER_OS", $os[1][0] );
	define( "USER_ONLINE_TIME", 150 );	// user is considered online before 3 minutes of inactivity



//-------------------------------------------------------------
//
//					 User Constants
//
//-------------------------------------------------------------

	//login msg
	define( "LOGIN_LOGOUT",     -4 );
	define( "LOGIN_WAIT",       -3 );
	define( "LOGIN_ERROR",      -2 );
	define( "LOGIN_BANNED",     -1 );
	define( "LOGIN_NOT_LOGGED",  0 );
	define( "LOGIN_DONE",        1 );
	define( "LOGIN_LOGGED",      2 );

	//user level
	global $user_level;
	$user_level = array( 	-3 => "USER_CONTACT",
							-2 => "USER_REFUSED",
							-1 => "USER_BANNED",
							 0 => "USER_UNREGISTERED",
							 1 => "USER_REGISTERED",
							 2 => "USER_ADMIN",
							 3 => "USER_SUPER_ADMIN" );

	// user status
	define( "USER_CONTACT",     -3 );
	define( "USER_REFUSED",     -2 );
	define( "USER_BANNED",      -1 );
	define( "USER_UNREGISTERED", 0 );
	define( "USER_REGISTERED",   1 );
	define( "USER_ADMIN",        2 );
	define( "USER_SUPER_ADMIN",  3 );


//-------------------------------------------------------------
//
//					Constants
//
//-------------------------------------------------------------

	//default value
	define( "PUBLISHED",true );
	define( "YES",      true );
	define( "NO",       false );
	define( "ENABLED",  true );
	define( "DISABLED", false );
	define( "LOW",      0 );
	define( "MED",      1 );
	define( "HIGH",     2 );

	//msg level
	define( "ERROR",   0 );
	define( "SUCCESS", 1 );
	define( "WARNING", 2 );
	define( "INFO",    3 );

	//time
	define( "TIME"		, time() );		// timestamp
	define( "SECOND"	, 1 );
	define( "MINUTE"	, 60 );			// seconds in minute
	define( "HOUR"		, 3600 );		// seconds in hour
	define( "DAY"		, 86400 );		// seconds in day
	define( "WEEK"		, 604800 );		// seconds in week
	define( "MONTH" 	, 2592000 );	// seconds in month
	define( "YEAR"  	, 31536000 );	// seconds in year
	define( "LEAP_YEAR" , 31622400 );	// seconds in leap year (every 4 year when february has 29 days)

	// google sitemaps
	// use for creating google sitemaps for your application
	global $changefreq;
	$changefreq = 	Array(
							-1 => "not in sitemaps",			// this content will not added to the sitemaps
							0 => "always",
							1 => "hourly",
							2 => "daily",
							3 => "weekly",
							4 => "monthly",
							5 => "yearly",
							6 => "never",
							 );


?>