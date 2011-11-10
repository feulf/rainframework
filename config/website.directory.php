<?php



//-------------------------------------------------------------
//
//      Directories
//
//-------------------------------------------------------------

        define( "SCRIPT_NAME",              basename($_SERVER['SCRIPT_NAME']) );
        define( "BASE_DIR",                 substr(dirname($_SERVER['SCRIPT_FILENAME']), 0, strrpos(dirname($_SERVER['SCRIPT_FILENAME']), '/') + 1) );

        // Rain folders
	define( "LIBRARY_DIR",              "../Rain/library/" );
        define( "LANGUAGE_DIR",             "../Rain/language/" );
        define( "CONSTANTS_DIR",            "../Rain/const/" );

        // website folders
	define( "CONFIG_DIR",               "../$website/config/" );
	define( "LOG_DIR",                  "../$website/log/" );
	define( "CACHE_DIR",                "../$website/cache/" );
	define( "UPLOADS_DIR",              "../$website/uploads/" );
	define( "JAVASCRIPT_DIR",           "../$website/js/" );
	define( "CSS_DIR",                  "../$website/css/" );

        // application folders
	define( "APPLICATION_DIR",          "../$application/" );
	define( "CONTROLLERS_DIR",          "../$application/controllers/" );
	define( "MODELS_DIR",               "../$application/models/" );
	define( "VIEWS_DIR",                "../$application/views/" );
        define( "APPLICATION_LIBRARY_DIR",  "../$application/library/" );



// -- end