<?php

//-------------------------------------------------------------
//
//      Directories
//
//-------------------------------------------------------------

	// Base application directory
    $base_dir = getcwd() . "/";
	chdir( $base_dir );
	set_include_path($base_dir);

	// base folder
	define( "BASE_DIR",					$base_dir );
	define( "BASE_NAME",				basename( $base_dir ) );

	// base folders
	define( "SYSTEM_DIR",				"system/" );
	define( "CONFIG_DIR",               "config/" );
	define( "CACHE_DIR",                "cache/" );
	define( "APPLICATION_DIR",          "$app/" );
	define( "WEBSITE_DIR",				"web/" );

	// Rain folders
	define( "LIBRARY_DIR",              "system/library/" );
    define( "LANGUAGE_DIR",             "system/language/" );
    define( "CONSTANTS_DIR",            "system/const/" );
	define( "LOG_DIR",                  "system/log/" );

    // website folders
	define( "UPLOADS_DIR",              "web/uploads/" );
	define( "JAVASCRIPT_DIR",           "web/js/" );
	define( "CSS_DIR",                  "web/css/" );
	define( "JQUERY_DIR",               "web/js/jquery" );

	
	// web application folders
	define( "MODULES_DIR",				"$app/modules/" );
	define( "TEMPLATES_DIR",			"$app/templates/" );
	
	// admin application folders
	define( "MODELS_DIR",				"$app/models/" );
	define( "VIEWS_DIR",				"$app/views/" );
	define( "CONTROLLERS_DIR",			"$app/controllers/" );



// -- end