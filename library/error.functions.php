<?php
 
/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


/** Debug and backtrace errors, just include in your project to make it works */

	// If you log the error you need a log/ directory with write permission
	if( !defined( "LOG_DIR" ) )
		define( "LOG_DIR", "application/log/" );

	global 	$error_n,
			$error_time,
			$error_levels,
			$error_reporting,
			$error_report_type,
			$error_log_file_type;

	//------------------------------------------
	// Configuration
	//------------------------------------------

	// By default debug is true
	if( !isset($GLOBALS['debug']) )
		$GLOBALS['debug'] = true;

	error_reporting( E_ALL | E_STRICT );				// Error reporting
	ini_set( "display_errors", 1 );						// If debug is true, display error and display startup errors
	ini_set( "html_errors", 0 );						// use html in errors

	$error_time = time();

	// error reported by email (only when $debug is false)
	// E_USER_ERROR | E_COMPILE_WARNING | E_COMPILE_ERROR | E_CORE_WARNING | E_CORE_ERROR | E_ERROR	
	// set null to disable, -1 to enable all errors
	$error_report_type = -1;

	// error logged on file (only when $debug is false)
	// E_USER_ERROR | E_COMPILE_WARNING | E_COMPILE_ERROR | E_CORE_WARNING | E_CORE_ERROR | E_ERROR	
	// set null to disable, -1 to enable all errors
	$error_log_file_type  = null;


	// errors name
	$error_levels = array(
	    E_USER_NOTICE => 'User Notice',
	    E_USER_WARNING => 'User Warning',
	    E_USER_ERROR => 'User Error',
	    E_COMPILE_WARNING => 'Compile warning',
	    E_COMPILE_ERROR => 'Compile Error',
	    E_CORE_WARNING => 'Core Warning',
	    E_CORE_ERROR => 'Core Error',
	    E_NOTICE => 'Notice',
	    E_WARNING => 'Warning',
	    E_ERROR => 'Error',
	    E_STRICT => 'Strict'
	);


	/**
	 * Custom Error Handler
	 *
	 */
	function myErrorHandler ( $errno, $errstr, $errfile, $errline ) {
		global $error_report_type, $error_reporting, $error_log_file_type, $error_n, $error_levels, $debug;
		$error_n++;

		$html = debug_error( $errstr, $errno, $errfile, $errline );

		if( $debug ){
			if( $error_n == 1 )
				echo "<style>.ee{border:1px solid #aaaaff;background:#f8f8ff;padding:10px;margin:10px;}</style>";
			echo $html;	// show error
		}
		elseif(  $errno & $error_report_type )
			// save all error information
			$error_reporting[$errstr] = array( 'errno'=>$errno, 'error'=>$error_levels[$errno], 'html'=>$html );
			
		if( $errno & $error_log_file_type  )				
			log_error( $html );					// log error
		
	}


	/**
	 * Debug the error showing files, lines and functions
	 * 
	 */
	function debug_error( $errstr, $errno, $errfile, $errline ){

			$html = '<div class="ee">'."\n";
			$html .= $GLOBALS['error_levels'][$errno] .': '.$errstr.' in <b>'.str_replace( $_SERVER['DOCUMENT_ROOT'], "", $errfile ) .'</b> on line <b>'.$errline.'</b><br/>';
			if( count( $debug_array = debug_backtrace() )>3 ){
				$html .= '<div class="ei">'."\n";
			    for( $i = 3, $n=count( $debug_array ); $i < $n; $i++ )
				   	$html .= "-" . str_replace( $_SERVER['DOCUMENT_ROOT'], "", isset( $debug_array[$i]['file'] ) ? $debug_array[ $i ]['file'] : null ) . " : " . ( isset($debug_array[$i]['line']) ? $debug_array[$i]['line'] : null ). "<br/>\n";
				$html .= "</div>";	   	
			}			
			$html .= "</div>";

			return $html;
	}
	
	

	/**
	 * Log the error
	 *
	 */
	function log_error( $html ){

		// add info about url and post var into error log
		$file = LOG_DIR . ( $date = date( "Y_m_d" ) ) . ".php";
		if( $GLOBALS['error_n'] == 1 )
			$html = ( !file_exists( $file ) ? "{$date} {$_SERVER['SERVER_SOFTWARE']} <br/>\n" : null ) . "<br/><br/>\n\n" . "<a name=\"{$GLOBALS['error_time']}\"></a>#{$GLOBALS['error_time']} {$_SERVER['REQUEST_URI']}" . ($_SERVER['REQUEST_METHOD']=='POST'?"POST: <br/>\n<pre>" . print_r( $GLOBALS['HTTP_POST_VARS'] , true ) : null ) . "</pre><br/><br/>\n\n" . $html;
		error_log( $html, 3, $file );
	}



	/**
	 * Retrive the error list
	 *
	 */
	function get_error_list(){
		global $error_reporting;
		return $error_reporting;
	}

	
	
	// set my error handler as default error handler
	set_error_handler( "myErrorHandler" );
	 

	
?>