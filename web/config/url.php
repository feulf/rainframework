<?php



//-------------------------------------------------------------
//
//      URLs
//
//-------------------------------------------------------------

	// public URL
	$url = ( isset($_SERVER['HTTPS']) ? "https://" : "http://" ) . $_SERVER["SERVER_NAME"] . dirname( $_SERVER['SCRIPT_NAME'] );
	if( !preg_match( '/.*\/$/', $url ) ) $url .= "/";

	// Base URLs
	define( "URL",							$url ); // website url

	// public URLs
 	define( "UPLOADS_URL",					URL . UPLOADS_DIR );
 	define( "JAVASCRIPT_URL",				URL . JAVASCRIPT_DIR );
 	define( "JQUERY_URL",					URL . JQUERY_DIR );
 	define( "CSS_URL",						URL . CSS_DIR );
 	define( "IMAGES_URL",					URL . IMAGES_DIR );


// -- end