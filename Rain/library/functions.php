<?php

/**
 *  RainFramework
 *  -------------
 *  Realized by Federico Ulfo & maintained by the Rain Team
 *  Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


/**
 *	Functions divided in categories
 */


//-------------------------------------------------------------
//
//					 INPUT FUNCTIONS
//
//-------------------------------------------------------------



	// disable register globals
	if( ini_get( "register_globals" ) && isset( $_REQUEST ) ) foreach ($_REQUEST as $k => $v) unset($GLOBALS[$k]);

	/**
	 * Get GET input
	 */
	function get( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){
                if( !$key )
                    return $filter ? filter_input_array( INPUT_GET, $filter ) : $_GET;
                if( isset($_GET[$key]) )
			return $filter ? filter_input(INPUT_GET, $key, $filter ) : $_GET[$key];
	}


	/**
	 * Get POST input
	 */
	function post( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){
                if( !$key )
                    return $filter ? filter_input_array( INPUT_POST, $filter ) : $_POST;
		if( isset($_POST[$key]) )
			return $filter ? filter_input(INPUT_POST, $key, $filter ) : $_POST[$key];
	}



	/**
	 * Get GET_POST input
	 */
	function get_post( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){
                if( !isset($GLOBALS['_GET_POST'] ) )
                    	$GLOBALS['_GET_POST'] = $_GET + $_POST;
                if( !$key )
                    return $filter ? filter_input_array( $GLOBALS['_GET_POST'], $filter ) : $GLOBALS['_GET_POST'];
		if( isset($GLOBALS['_GET_POST'][$key] ) )
			return $filter ? filter_input(INPUT_GET & INPUT_POST, $key, $filter ) : $GLOBALS['_GET_POST'][$key];
	}



	/**
	 * Get COOKIE input
	 */
	function cookie( $key = null, $filter = FILTER_SANITIZE_MAGIC_QUOTES ){
		if( isset($_COOKIE[$key]) )
			return $filter ? filter_input(INPUT_COOKIE, $key, $filter ) : $_COOKIE[$key];
	}



//-------------------------------------------------------------
//
//	BENCHMARK/DEBUG FUNCTIONS
//
//-------------------------------------------------------------


	/**
	 * Useful for debug, print the variable $mixed and die
	 */
	function dump( $mixed, $exit = 1 ){
		echo "<pre>dump \n---------------------- \n\n" . print_r( $mixed, true ) . "\n----------------------<pre>";
		if( $exit ) exit;
	}



	/**
	 * Save the memory used at this point
	 */
	function memory_usage_start( $memName = "execution_time" ){
            return $GLOBALS['memoryCounter'][$memName] = memory_get_usage();
	}



	/**
	 * Get the memory used
	 */
	function memory_usage( $memName = "execution_time", $byte_format = true ){
            $totMem = memory_get_usage() - $GLOBALS['memoryCounter'][ $memName ];
            return $byte_format ? byte_format($totMem) : $totMem;
	}


//-------------------------------------------------------------
//
//					 TIME FUNCTIONS
//
//-------------------------------------------------------------

	/**
	 * Start the timer
	 */
	function timer_start( $timeName = "execution_time" ){
		$stimer = explode( ' ', microtime( ) );
                $GLOBALS['timeCounter'][$timeName] = $stimer[ 1 ] + $stimer[ 0 ];
	}

	/**
	 * Get the time passed
	 */
	function timer( $timeName = "execution_time", $precision = 6 ){
	       $etimer = explode( ' ', microtime( ) );
	       $timeElapsed = $etimer[ 1 ] + $etimer[ 0 ] - $GLOBALS['timeCounter'][ $timeName ];
	       return substr( $timeElapsed, 0, $precision );
	}

	/**
	 * Transform timestamp to readable time format
	 *
	 * @param int $time unix timestamp
	 * @param string format of time (use the constant fdate_format or ftime_format)
	 */
	function time_format( $time=null, $format=DATE_FORMAT ){
		return strftime( $format, $time );
	}


	/**
	 * Transform timestamp to readable time format as elapsed time e.g. 3 days ago, or 5 minutes ago to a maximum of a week ago
	 *
	 * @param int $time unix timestamp
	 * @param string format of time (use the constant fdate_format or ftime_format)
	 */
	function time_elapsed( $time = null, $format ){

		$diff = TIME - $time;
		if( $diff < MINUTE )
			return $diff . " " . get_msg('seconds_ago');
		elseif( $diff < HOUR )
			return ceil($diff/60) . " " . get_msg('minutes_ago');
		elseif( $diff < 12*HOUR )
			return ceil($diff/3600) . " " . get_msg('hours_ago');
		elseif( $diff < DAY )
			return get_msg('today') . " " . strftime( TIME_FORMAT, $time );
		elseif( $diff < DAY*2 )
			return get_msg('yesterday') . " " . strftime( TIME_FORMAT, $time );
		elseif( $diff < WEEK )
			return ceil($diff/DAY) . " " . get_msg('days_ago') . " " . strftime( TIME_FORMAT, $time );
		else
			return strftime( $format, $time );
	}


	/**
	 * Convert seconds to hh:ii:ss
	 */
	function sec_to_hms($sec) {
		$hours = intval(intval($sec) / 3600);
		$hms  = str_pad($hours, 2, "0", STR_PAD_LEFT). ':';
		$minutes = intval(($sec / 60) % 60);
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
		$seconds = intval($sec % 60);
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
		return $hms;
	}



	/**
	 * Convert seconds to string, eg. "2 minutes", "1 hour", "16 seconds"
	 */
	function sec_to_string($sec) {
		$str = null;
		if( $hours = intval(intval($sec) / 3600) )
			$str .= $hours > 1 ? $hours . " " . get_msg('hours') : $hours . " " . get_msg('hour');
		if( $minutes = intval(($sec / 60) % 60) )
			$str .= $minutes > 1 ? $minutes . " " . get_msg('minutes') : $minutes . " " . get_msg('minute');
		if( $seconds = intval($sec % 60) )
			$str .= $seconds > 1 ? $seconds . " " . get_msg('seconds') : $seconds . " " . get_msg('second');
		return $str;
	}


//-------------------------------------------------------------
//
//					 STRING FUNCTIONS
//
//-------------------------------------------------------------



	/**
	 * Cut html
	 * text, length, ending, tag allowed, $remove_image true / false, $exact true=the ending words are not cutted
	 * Note: I get this functions from web but I don't remember the source. It should be from cakePHP.
	 */
	function cut_html( $text, $length = 100, $ending = '...', $allowed_tags = '<b><i>', $remove_image = true, $exact = false ) {

		if( !$remove_image )
			$allowed_tags .= '<img>';

		$text = strip_tags($text, $allowed_tags );
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
			return $text;

		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag (f.e. </b>)
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
						unset($open_tags[$pos]);
					}
				// if tag is an opening tag (f.e. <b>)
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length)
				break;
		}

		// don't cut the last words
		if (!$exact && $spacepos = strrpos($truncate, ' ') )
			$truncate = substr($truncate, 0, $spacepos);

		$truncate .= $ending;
		foreach ($open_tags as $tag)
			$truncate .= '</' . $tag . '>';

		return $truncate;
	}



	/**
	 * Cut string and add ... at the end
	 * useful to cut noHTML text, for example to cut the title of an article
	 */
	function cut( $string, $length, $ending = "..." ){
		if( strlen( $string ) > $length )
			return $string = substr( $string, 0, $length ) . $ending;
		else
			return $string = substr( $string, 0, $length );
	}




	/**
	 * Return a random string
	 */
	function rand_str($length = 5, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'){
	    $chars_length = (strlen($chars) - 1);
	    $string = $chars{rand(0, $chars_length)};
	    for ($i = 1; $i < $length; $i = strlen($string)){
	        $r = $chars{rand(0, $chars_length)};
	        if ($r != $string{$i - 1}) $string .=  $r;
	    }
	    return $string;
	}



//-------------------------------------------------------------
//
//					NUMBER FUNCTIONS
//
//-------------------------------------------------------------

	/**
	 * Convert byte to more readable format, like "1 KB" instead of "1024".
	 * cut_zero, remove the 0 after comma ex:  10,00 => 10      14,30 => 14,3
	 */
	function byte_format( $size ){
		if( $size > 0 ){
		    $unim = array("B","KB","MB","GB","TB","PB");
		    for( $i=0; $size >= 1024; $i++ )
		        $size = $size / 1024;
		    return number_format($size,$i?2:0,DEC_POINT,THOUSANDS_SEP)." ".$unim[$i];
		}
	}


	/**
	 * Format the money in the current format. If add_currency is true the function add the currency configured into the language
	 */
	function format_money( $number, $add_currency = false ){
		return ( $add_currency && CURRENCY_SIDE == 0 ? CURRENCY . " " : "" ) . number_format($number,2,DEC_POINT,THOUSANDS_SEP) . ( $add_currency && CURRENCY_SIDE == 1 ? " " . CURRENCY : "" );
	}




//-------------------------------------------------------------
//
//					EMAIL FUNCTIONS
//
//-------------------------------------------------------------


	/**
	 * Return true if the email is valid
	 */
	function is_email( $string ){
		return eregi( "^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $string );
	}

	/**
	 * Send an email
	 * @param $to
	 */
	function email_send( $to, $subject, $body, $from = null, $from_name = null, $attachment = null, $embed_images = false ){

            // TO DO: use the email class

	}


	/**
	 * Send an email with selected template
	 */
	function email_tpl_send( $template = "generic/email", $to, $subject, $body, $from = null, $from_name = null, $attachment = null){
		$tpl = new TPL();
		$tpl->assign("body", $body );
		$body = $tpl->draw( $template, true );
		return emailSend( $to, $subject, $body, $from, $from_name, $attachment );
	}



//-------------------------------------------------------------
//
//					FILE FUNCTIONS
//
//-------------------------------------------------------------

	/**
	 * Return list of dir and files without . ..
	 *
	 * @param string $d directory
	 */
	function dir_scan($dir){
		if( is_dir($dir) && $dh = opendir($dir) ){ $f=array(); while ($fn = readdir($dh)) { if($fn!='.'&&$fn!='..') $f[] = $fn; } return $f; }
	}

	/**
	 * Get the list of files filtered by extension ($ext)
	 *
	 * @param string $d directory
	 * @param string $ext extension filter, example ".jpg"
	 */
	function file_list($dir,$ext=null){
		if( $dl=dir_scan($dir) ){ $l=array(); foreach( $dl as $f ) if( is_file($dir.'/'.$f) && ($ext?preg_match('/\.'.$ext.'$/',$f):1) ) $l[]=$f; return $l; }
	}



	/**
	 * Get the list of directory
	 *
	 * @param string $dir directory
	 */
	function dir_list($dir){
		if( $dl=dir_scan($dir) ){ $l=array(); foreach($dl as $f)if(is_dir($dir.'/'.$f))$l[]=$f; return $l; }
	}


	/**
	 * File extension
	 *
	 * @param string $file filename
	 */
	function file_ext($filename){
		return substr(strrchr($filename, '.'),1);
	}



	/**
	 * Get the name without extension
	 *
	 * @param string $f filename
	 */
	function file_name($filename){
		if( ($filename = basename($filename) ) && ( $dot_pos = strrpos( $filename , "." ) ) )
			return substr( $filename, 0, $dot_pos );
	}



	/**
	 * Delete dir and contents
	 *
	 * @param string $dir directory
	 */
	function dir_del($dir) {
		if( $l=dir_scan($dir) ){ foreach($l as $f) if (is_dir($dir."/".$f)) dir_del($dir.'/'.$f);	else unlink($dir."/".$f);	return rmdir($dir); }
	}

	/**
	 * Copy all the content of a directory
	 *
	 * @param string $s source directory
	 * @param string $d destination directory
	 */
	function dir_copy( $source, $dest) {
		if (is_file($source)){
			copy($source, $dest);
			chmod($dest, fileperms($source) );
		}
		else{
			mkdir( $dest, 0777 );
			if( $l=dir_scan($source) ){ foreach( $l as $f ) dir_copy("$source/$f", "$dest/$f"); }
		}
	}



	/**
	 * Upload one file selected with $file. Use it when you pass only one file with a form.
	 * The file is saved into UPS_DIR, the name created as "md5(time()) . file_extension"
	 * it return the filename
	 *
	 * @return string uploaded filename
	 */
	function upload_file($file){
		if( $_FILES[$file]["tmp_name"] ){
			move_uploaded_file( $_FILES[$file]["tmp_name"], UPS_DIR . ( $filename = md5(time()).".".( strtolower( file_ext($_FILES[$file]['name'] ) ) ) ) );
			return $filename;
		}
	}


	/**
	 * Upload an image file and create a thumbnail
	 *
	 * @param string $file
	 * @param string $upload_dir
	 * @param string $thumb_prefix Prefisso della thumbnail
	 * @param int $max_width
	 * @param int $max_height
	 * @param bool $square
	 * @return string Nome del file generato
	 */
	function upload_image( $file, $thumb_prefix = null, $max_width = 128, $max_height = 128, $square = false ){
		if( $filename = upload_file( $file ) ){
			//se voglio creare la thumb e $square=true, tento di creare la thumbnails
			if( $thumb_prefix && !image_resize( UPS_DIR . $filename,  UPS_DIR . $thumb_prefix . $filename, $max_width, $max_height, $square ) ){
				unlink( UPS_DIR . $filename );
				return false;
			}
			return $filename;
		}
	}




//-------------------------------------------------------------
//
//					IMAGE FUNCTIONS
//
//-------------------------------------------------------------



	/**
	 * Create thumb from image
	 */
	function image_resize( $source, $dest, $maxx = 100, $maxy = 100, $square = false, $quality = 70 ){

		switch( $ext = file_eXT( $source ) ){
			case 'jpg':
			case 'jpeg':	$source_img = imagecreatefromjpeg( $source );	break;
			case 'png':		$source_img = imagecreatefrompng( $source );	break;
			case 'gif':		$source_img = imagecreatefromgif( $source );	break;
			default:		return false;
		}

		list($width, $height) = getimagesize( $source );
		if( $square ){
			$new_width = $new_height = $maxx;
			if( $width > $height ) {
				$x = ceil( ( $width - $height ) / 2 );
				$width = $height;
			} else{
				$y = ceil( ( $height - $width ) / 2 );
				$height = $width;
			}
		}
		else{
			if( $maxx != 0 && $maxy != 0 ){
				if( $maxx < $width or $maxy < $height ){
					$percent1 = $width / $maxx;
					$percent2 = $height / $maxy;
					$percent = max($percent1,$percent2);
					$new_height = round($height/$percent);
					$new_width = round($width/$percent);
				}
			}
			elseif( $maxx == 0 && $maxy != 0 ){
				if( $height > $maxy ){
					$new_height = $maxy;
					$new_width = $width * ( $maxy / $height );
				}
			}
			else{
				if( $width > $maxx ){
					$new_width = $maxx;
					$new_height = $height * ( $maxx / $width );
				}
			}
		}

		if( !isset($new_width) or !$new_width )
			$new_width = $width;
		if( !isset($new_height) or !$new_height )
			$new_height = $height;

		$dest_img = ImageCreateTrueColor($new_width, $new_height);
		imageCopyResampled( $dest_img, $source_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

		switch( $ext ){
			case 'png': imagepng( $dest_img, $dest, $quality ); break;
			case 'gif': imagegif( $dest_img, $dest, $quality ); break;
			default:	imagejpeg( $dest_img, $dest, $quality );
		}

		imagedestroy( $source_img );
		imagedestroy( $dest_img );
	}




//-------------------------------------------------------------
//
//					HOOKS FUNCTIONS
//
//-------------------------------------------------------------


	/**
	 * Hooks allows to load files, execute classes or execute functions,
	 * defined into globals $hooks variable. You can set the code you want to execute
	 * by calling hooks_add_file, hooks_add_function, hooks_add_class
	 *
	 * @param string $name Name of the hooks
	 *
	function hooks($name){
		global $hooks;
		if( isset($hooks[$name]) && is_array( $hooks[$name] ) ){
			foreach( $hooks[$name] as $hook ){

				$file = $hook['file'];
				$class = $hook['class'];
				$function = $hook['function'];
				$params = $hook['params'];

				if( $file ){
					if( file_exists($file) ){
						if( $class or $function )
							require_once $file;
						else
							require $file;
					}
					else
						trigger_error('HOOKS: FILE NOT FOUND',E_WARNING);
				}

				if( $class ){
					if( class_exists($class) ){

						for($i=0,$n=count($params),$param="";$i<$n;$i++)
							$param .= $i>0 ? ',$params['.$i.']' : '$params['.$i.']';

						if( !$function or $function==$class )
							eval( '$obj = new $class('.$param.')' );
						else{
							$obj = new $class;
							if( is_callable(array($obj,$function) ) )
								eval( '$obj->$function( ' . $param . ' );' );
							else
								trigger_error("HOOKS: METHOD NOT FOUND OR NOT CALLABLE",E_WARNING);
						}

					}
					else
						trigger_error('HOOKS: CLASS NOT FOUND',E_WARNING);

				}elseif( $function ){
					if( function_exists($function) )
						$function($params);
					else
						trigger_error('HOOKS: FUNCTION NOT FOUND',E_WARNING);
				}
			}
		}

	}


	/**
	 * You can add a function or a method
	 *
	function hooks_add_function($name,$function,$params=null,$file=null){
		global $hooks;
		$hooks[$name][] = array( 'file'=>$file,'class'=>null, 'function'=>$function, 'params'=>$params );
	}


	/**
	 * You can add a method
	 *
	function hooks_add_class($name,$class,$function=null,$params=null,$file=null){
		global $hooks;
		$hooks[$name][] = array( 'file'=>$file,'class'=>$class,'function'=>$function, 'params'=>$params );
	}

	/**
	 * It add a file to hooks, HTML or PHP is ok.
	 *
	function hooks_add_file($name,$file){
		global $hooks;
		$hooks[$name][] = array( 'file'=>$file,'class'=>null,'function'=>null,'params'=>null );
	}

	*/



//-------------------------------------------------------------
//
//					Settings
//
//-------------------------------------------------------------

	function get_setting( $key = null ){
		global $settings;
		if( !$key )
			return $settings;
		if( isset( $settings[$key] ) )
			return $settings[$key];
	}



//-------------------------------------------------------------
//
//					 Language
//
//-------------------------------------------------------------

	/**
	 * Get the translated string if in language dictionary, return the string if not
	 *
	 * @param string $msg Msg to translate
	 * @param string $modifier You can choose a modifier from: strtoupper, strtolower, ucwords, ucfirst
	 * @return translated string
	 */
	function get_msg( $msg, $modifier = null ){
		global $lang;
		if( isset($lang[$msg]))
			$msg = $lang[$msg];
		return $modifier ? $modifier($msg) : $msg;
	}

	function get_lang(){
		return LANG_ID;
	}

	function load_lang( $file ){
		require_once LANGUAGE_DIR . get_lang() . "/" . $file . ".php";
	}

	// draw a message styled as SUCCESS, WARNING, ERROR or INFO. See .box in style.css for the style
	function draw_msg( $msg, $type = SUCCESS, $close = false, $autoclose = 0 ){
		add_script("jquery/jquery.min.js" );
		add_style( "box.css", CSS_DIR );
		$box_id = rand(0,9999) . "_" . time();
		if( $close )
			$close = '<div class="close"><a onclick="$(\'#box_'.$box_id.'\').slideUp();">x</a></div>';
		if($autoclose)
			add_javascript( 'setTimeout("$(\'#box_'.$box_id.'\').slideUp();", "'.($autoclose*1000).'")', $onload=true );

		switch( $type ){
			case SUCCESS: 	$class = 'success'; break;
			case WARNING: 	$class = 'warning'; break;
			case ERROR:  	$class = 'error'; break;
			case INFO: 	$class = 'info'; break;
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
	function add_style( $style_file, $dir = CSS_DIR ){
		$GLOBALS['style'][$dir . $style_file] = URL . $dir . $style_file;
	}

	//add javascript file
	function add_script( $script_file, $dir = JAVASCRIPT_DIR ){
		$GLOBALS['script'][$dir . $script_file] = URL . $dir . $script_file;
	}

	//add javascript code
	function add_javascript( $javascript, $onload = false ){
		if( !$onload )
			$GLOBALS['javascript'] .= "\n".$javascript."\n";
		else
			$GLOBALS['javascript_onload'] .= "\n".$javascript."\n";
	}

	/**
	 * get javascript
	 */
	function get_javascript( $compress = false ){
                global $script, $javascript, $javascript_onload;
		$html = "";
		if( $script )
			foreach( $script as $s )
				$html .= '<script src="'.$s.'" type="text/javascript"></script>' . "\n";
		if( $javascript_onload ) $javascript .=  "\n" . "$(function(){" . "\n" . "	$javascript_onload" . "\n" . "});" . "\n";
		if( $javascript ) $html .= "<script type=\"text/javascript\">" . "\n" .$javascript . "\n" . "</script>";
		return $html;
	}

	/**
	 * get the style
	 */
	function get_style(){
		global $style;
		$html = "";
		if( $style )
			foreach( $style as $s )
				$html .= '	<link rel="stylesheet" href="'.$s.'" type="text/css" />' . "\n";
		return $html;
	}


//-------------------------------------------------------------
//
//					LOCALIZATION FUNCTIONS
//
//-------------------------------------------------------------

        function get_ip(){
            if( !defined("IP") ){
                $ip = getenv( "HTTP_X_FORWARDED_FOR" ) ? getenv( "HTTP_X_FORWARDED_FOR" ) : getenv( "REMOTE_ADDR" );
                if( !preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip ) ) $ip = null;
                define( "IP", $ip );
            }
            return IP;
        }



	/**
	 * Return true if $ip is a valid ip
	 */
	function is_ip($ip){
	    return preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip );
	}



	/**
	 * Return the array with all geolocation info of user selected by IP
	 */
	define( "IPINFODB_KEY", "YOUR_KEY" );
	function ip_to_location( $ip = IP ){
		if( is_ip( $ip ) )
			return json_decode( file_get_contents( "http://api.ipinfodb.com/v2/ip_query.php?key=".IPINFODB_KEY."&ip={$ip}&output=json&timezone=true" ) );
	}


        /**
         * Return the browser information of the logged user
         */
        function get_browser_info(){

            if( !isset( $GLOBALS['rain_browser_info'] ) ){
                $known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko');
                preg_match( '#(' . join('|', $known) . ')[/ ]+([0-9]+(?:\.[0-9]+)?)#', strtolower($_SERVER['HTTP_USER_AGENT']), $br );
                preg_match_all( '#\((.*?);#', $_SERVER['HTTP_USER_AGENT'], $os );

                global $rain_browser_info;
                $rain_browser_info['lang_id'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                $rain_browser_info['browser'] = isset( $br[1][1] ) ? $br[1][1] : null;
                $rain_browser_info['version'] = isset( $br[2][1] ) ? $br[2][1] : null;
                $rain_browser_info['os'] = $od[1][0];

            }
            return $GLOBALS['rain_browser_info'];


        }



// -- end