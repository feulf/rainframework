<?php

/**
 *	Rain Framework > Rain Functions
 *	--------------------------
 *  
 *	This functions are divided in categories: Input, Time, String, Email, File, Image, Generic
 * 
 *	@author Federico Ulfo
 *	@copyright developed and mantained by the Rain Team: http://www.raintm.com
 *	@license Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *	@link http://www.rainframework.com
 *	@package RainFramework
 */



//-------------------------------------------------------------
//
//					 Language
//
//-------------------------------------------------------------

	function get_msg( $msg ){
		global $lang;
		return isset( $lang[$msg] ) ? $lang[$msg] : null;
	}

	function get_lang(){
		return LANG_ID;
	}

	function load_lang( $file ){
		require_once LANGUAGE_DIR . LANG_ID . "/" . $file . ".php";
	}
	
	
	// draw a message styled as SUCCESS, WARNING, ERROR or INFO. See .box in style.css for the style
	function draw_msg( $msg, $type = SUCCESS, $close = false, $autoclose = 0 ){
		add_script("jquery/jquery.min.js" );
		add_style( "box.css", APPLICATION_LIBRARY_DIR . "css/" );
		$box_id = rand(0,9999) . "_" . time();
		if( $close )
			$close = '<div class="close"><a onclick="$(\'#box_'.$box_id.'\').slideUp();">x</a></div>';
		if($autoclose)
			addJavascript( 'setTimeout("$(\'#box_'.$box_id.'\').slideUp();", "'.($autoclose*1000).'")', $onload=true );

		switch( $type ){
			case SUCCESS: 	$class = 'success'; break;
			case WARNING: 	$class = 'warning'; break;
			case ERROR:  	$class = 'error'; break;
			case INFO: 		$class = 'info'; break;
		}

		// style defined in style.css as .box
		return '<div class="box box_'.$class.'" id="box_'.$box_id.'">'.$close.$msg.'</div>';
	}



//-------------------------------------------------------------
//
//					 Javascript & CSS
//
//-------------------------------------------------------------
	
	//style sheet and javascript
	global $style, $script, $javascript, $javascript_onload;
	$style = $script = array();
	$javascript = $javascript_onload = "";


	//add style sheet
	function add_style( $style_file, $dir ){
		$GLOBALS['style'][$style_file] = URL . $dir . $style_file;
	}

	//add javascript file
	function add_script( $script_file, $dir = JAVASCRIPT_DIR ){
		$GLOBALS['script'][$script_file] = URL . $dir . $script_file;
	}

	//add javascript code
	function add_javascript( $javascript, $onload = false ){
		if( !$onload )
			$GLOBALS['javascript'] .= "\n".$javascript."\n";
		else
			$GLOBALS['javascript_onload'] .= "\n".$javascript."\n";
	}	
	

		
?>