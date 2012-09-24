<?
$db=array(
	// development database (default)
	'dev'=>array(
		'driver'=>'mysql',
		'hostname'=>'localhost',
		'username'=>'root',
		'password'=>'root',
		'database'=>'rainframework2'
	),
	//production database (live website)
	'prod'=>array(
		'driver'=>'',
		'hostname'=>'',
		'username'=>'',
		'password'=>'',
		'database'=>''
	)
);

if( !defined("DB_PREFIX" ) )
	define( "DB_PREFIX", "RAIN_" );
// -- end
