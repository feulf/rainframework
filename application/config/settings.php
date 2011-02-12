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




//-------------------------------------------------------------
//
//					File Constants
//
//-------------------------------------------------------------
	
	// thumbnail prefix, are the prefix of the image file used as thumbnails
	define( "THUMB_PREFIX", "t.");		
	
	// File type
	global $file_type;
	$file_type = array( 1 => "image",
						2 => "audio",
						3 => "video",
						4 => "document", 
						5 => "archive" );

	// File type
	define( "IMAGE",    1 );
	define( "AUDIO",    2 );
	define( "VIDEO",    3 );
	define( "DOCUMENT", 4 );
	define( "ARCHIVE",  5 );

	// File extension
	define( "IMAGE_EXT"		, "jpg,jpeg,gif,png" );
	define( "AUDIO_EXT"		, "mp3" );
	define( "VIDEO_EXT"		, "flv,mov" );
	define( "DOCUMENT_EXT"	, "doc,docx,pdf,xls,csv,xlsx,txt,ttf,rtf" );
	define( "ARCHIVE_EXT"	, "zip,rar,gzip" );

	
	
?>