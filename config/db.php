<?php


    // development database (default)
    $db['dev']['driver']    = 'mysql';
    $db['dev']['hostname']  = 'localhost';
    $db['dev']['username']  = 'root';
    $db['dev']['password']  = 'root';
    $db['dev']['database']  = 'rainframework2';

    // production database (live website)
    $db['prod']['driver']    = '';
    $db['prod']['hostname']  = '';
    $db['prod']['username']  = '';
    $db['prod']['password']  = '';
    $db['prod']['database']  = '';

	if( !defined("DB_PREFIX" ) )
		define( "DB_PREFIX", "RAIN_" );

// -- end