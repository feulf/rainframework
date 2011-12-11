<?php

	// default database	

	$server = "mysql";
	$hostname = "localhost";
    $username = "root";
	$password = "root";
	$database = "rainframework2";

	if( !defined("DB_PREFIX" ) )
		define( "DB_PREFIX", "RAIN_" );
	
?>