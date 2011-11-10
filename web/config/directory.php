<?php



//-------------------------------------------------------------
//
//      Directories
//
//-------------------------------------------------------------

	define( "SCRIPT_NAME",              basename($_SERVER['SCRIPT_NAME']) );
	define( "BASE_DIR",                 dirname($_SERVER['SCRIPT_NAME']) );
	//define( "BASE_DIR",               substr(dirname($_SERVER['SCRIPT_FILENAME']), 0, strrpos(dirname($_SERVER['SCRIPT_FILENAME']), '/') + 1) );

    // Rain folders
	define( "LIBRARY_DIR",              "system/library/" );
	define( "LANGUAGE_DIR",             "system/language/" );
	define( "CONSTANTS_DIR",            "system/const/" );

    // website folders
	define( "CONFIG_DIR",               "web/config/" );
	define( "LOG_DIR",                  "web/log/" );
	define( "CACHE_DIR",                "web/cache/" );
	define( "UPLOADS_DIR",              "web/uploads/" );
	define( "JAVASCRIPT_DIR",           "web/js/" );
	define( "CSS_DIR",                  "web/css/" );

        // application folders
	define( "APPLICATION_DIR",          "$app/" );
	define( "CONTROLLERS_DIR",          "$app/controllers/" );
	define( "MODELS_DIR",               "$app/models/" );
	define( "VIEWS_DIR",                "$app/views/" );
    define( "APPLICATION_LIBRARY_DIR",  "$app/library/" );



// -- end