<?php

/**
 *  Functions divided in categories: Input, Time, String, Email, File, Image, Generic
 * 
 *  @author Federico Ulfo <rainelemental@gmail.com> | www.federicoulfo.it
 *  @copyright RainFramework is under GNU/LGPL 3 License
 *  @link http://www.rainframework.com
 *  @version 2.0
 *  @package RainFramework
 */




//-------------------------------------------------------------
//
//					 INPUT FUNCTIONS
//
//-------------------------------------------------------------
	

	// Input filter

	if( ini_get( "register_globals" ) && isset( $_REQUEST ) ) foreach ($_REQUEST as $k => $v) unset($GLOBALS[$k]);		// disable register globals		
	global $_GET_FILTER, $_POST_FILTER, $_GET_POST_FILTER, $_GET_POST;
	$_GET_FILTER = $_GET;
	$_POST_FILTER = $_POST;
	$_GET_POST = $_GET + $_POST;
	if( !get_magic_quotes_gpc() ){
		replace_sql_injection( $_GET_FILTER );
		replace_sql_injection( $_POST_FILTER );
		$_GET_POST_FILTER = $_GET_FILTER + $_POST_FILTER;
	}
	// @access private
	function replace_sql_injection( &$a ){ if( is_array( $a ) ) foreach( $a as $k => &$v ) if( is_array( $a[$k] ) ) replace_sql_injection($a[$k]); else $a[$k] = addslashes($v); }
	


	/**
	 * Get GET input
	 * If key = null, return array GET + POST
	 */
	function get( $key = null, $filter = true ){
		return $filter ? ( !$key ? $GLOBALS['_GET_FILTER'] : ( isset( $GLOBALS['_GET_FILTER'][$key] ) ? $GLOBALS['_GET_FILTER'][$key] : null ) ) : ( !$key ? $_GET : ( isset( $_GET[$key] ) ? $_GET[$key] : null ) );
	}
	
	
	/**
	 * Get POST input
	 * If key = null, return array GET + POST
	 */
	function post( $key = null, $filter = true ){
		return $filter ? ( !$key ? $GLOBALS['_POST_FILTER'] : ( isset( $GLOBALS['_POST_FILTER'][$key] ) ? $GLOBALS['_POST_FILTER'][$key] : null ) ) : ( !$key ? $_POST : ( isset( $_POST[$key] ) ? $_POST[$key] : null ) );
	}
	
	
	/**
	 * Get POST input
	 * If key = null, return array GET + POST
	 */
	function getPost( $key = null, $filter = true ){
		return $filter ? ( !$key ? $_GET_POST_FILTER : ( isset( $_GET_POST_FILTER[$key] ) ? $_GET_POST_FILTER[$key] : null ) ) : ( !$key ? $_GET_POST : ( isset( $_GET_POST[$key] ) ? $_GET_POST[$key] : null ) );
	}

		
	
//-------------------------------------------------------------
//
//					 BENCHMARK
//
//-------------------------------------------------------------

	/**
	 * Start the timer
	 */
	function memory_usage_start( $memName = "execution_time" ){
        $GLOBALS['memoryCounter'][$memName] = memory_get_usage();
	}

	/**
	 * Get the time passed
	 */
	function memory_usage( $timeName = "execution_time" ){
	       return byteFormat( memory_get_usage() - $GLOBALS['memoryCounter'][ $memName ] );
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
	 * Transform timestamp to readable time format as time passed e.g. 3 days ago, or 5 minutes ago to a maximum of a week ago
	 * 
	 * @param int $time unix timestamp
	 * @param string format of time (use the constant fdate_format or ftime_format)
	 */
	function time_passed( $time = null, $format ){

		$diff = TIME - $time;
		if( $diff < MINUTE )
			return $diff . " " . _SECONDS_AGO_;
		elseif( $diff < HOUR )
			return ceil($diff/60) . " " . _MINUTES_AGO_;
		elseif( $diff < 12*HOUR )
			return ceil($diff/3600) . " " . _HOURS_AGO_;
		elseif( $diff < DAY )
			return _TODAY_ . " " . strftime( TIME_FORMAT, $time );
		elseif( $diff < DAY*2 )
			return _YESTERDAY_ . " " . strftime( TIME_FORMAT, $time );
		elseif( $diff < WEEK )
			return ceil($diff/DAY) . " " . _DAYS_AGO_ . " " . strftime( TIME_FORMAT, $time );
		else
			return strftime( $format, $time );
	}
	

	/**
	 * Convert seconds to hh:ii:ss
	 */
	function sec2hms($sec) {
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
	function sec2string($sec) {
		$str = null;
		if( $hours = intval(intval($sec) / 3600) )
			$str .= $hours > 1 ? $hours . " " . _HOURS_ : $hours . " " . _HOUR_;
		if( $minutes = intval(($sec / 60) % 60) )
			$str .= $minutes > 1 ? $minutes . " " . _MINUTES_ : $minutes . " " . _MINUTE_;
		if( $seconds = intval($sec % 60) )
			$str .= $seconds > 1 ? $seconds . " " . _SECONDS_ : $seconds . " " . _SECOND_;
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
	 * Note: I get this functions from web but I don't remember the source, if somebody know please tell me to give credits
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

	

//-------------------------------------------------------------
//
//					EMAIL FUNCTIONS
//
//-------------------------------------------------------------


	/**
	 * Return true if the email is valid
	 */
	function isEmail( $string ){
		return eregi( "^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $string );
	}

	/**
	 * Send an email
	 * @param $to
	 */
	function emailSend( $to, $subject, $body, $from = null, $from_name = null, $attachment = null, $embed_images = false ){

		require_once INC_DIR . "phpmailer/class.phpmailer.php";
		require_once CONF_DIR . "mail.conf.php";

		global $mail_type, $mail_host, $mail_username, $mail_password, $mail_charset;

		$mail = new PHPMailer();

		if( MAIL_TYPE == 'smtp' ){
			$mail->IsSMTP();					// send via SMTP
			$mail->Host      =  MAIL_HOST;		// smtp servers
			$mail->SMTPAuth  =  true;     		// turn on SMTP authentication
			$mail->Username  =	MAIL_USERNAME;	// SMTP username
			$mail->Password  =  MAIL_PASSWORD;	// SMTP password
		}
		else
			$mail->isMail();

		$mail->From      =  $from;
		$mail->FromName  =  $from_name;

		$mail->IsHTML(true);								// send as HTML
		$mail->CharSet  =  CHARSET;
		$mail->Subject  =  $subject;

		
		//---------------------------------------------
		//  Embed image in email
		//---------------------------------------------
		if( $embed_images ){
			preg_match_all( '#(\<img(?:.*?))src="(.*?)"((?:.*?)\>)#i', $body, $match );		
			$embed_image = array();
			for( $i = 0, $n=count($match[0]); $i < $n; $i++ ){
				$tag = $match[0][$i];
				$src = $match[2][$i];
				if( substr( $src, 0, 7 ) != "http://" ){
					$embed_image[] = $src;
					$ext = fileExt($src);
					$body = eregi_replace( $tag, $match[1][$i] . 'src="cid:img_'.$i.'"' . $match[3][$i], $body );
					$mail->AddEmbeddedImage( $src, "img_{$i}", "", "base64", "image/$ext" );	// src, id, name, econding, type
				}
			}
		}
		//---------------------------------------------

		$mail->Body     =  $body;

		// get the recipient
		if( is_string( $to ) && count( $array_to = preg_split( "#,|;#", $to  ) ) > 0 )
				foreach( $array_to as $to_email )
					$recipient[$to_email] = $to_email;					
		elseif( is_array( $to ) )
			$recipient = $to;

		global $mailer_error_email;
		$error = false;

		foreach( $recipient as $email => $name ){
			$mail->AddAddress( $email, $name );
			if( !$mail->Send() ){
				$mailer_error_email[] = $email;
				$error = true;
			}
			$mail->ClearAddresses();
		}
		return !$error;
	}
	
	
	/**
	 * Send an email with selected template
	 */
	function emailTPLSend( $template = "generic/email", $to, $subject, $body, $from = null, $from_name = null, $attachment = null){
		$tpl = new RainTPL();
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
	function dirScan($d){
		if( is_dir($d) && $dh = opendir($d) ){ $f=array(); while ($fn = readdir($dh)) { if($fn!='.'&&$fn!='..') $f[] = $fn; } return $f; }
	}
	
	/**
	 * Get the list of files filtered by extension ($x) 
	 *
	 * @param string $d directory
	 * @param string $x extension filter, example ".jpg"
	 */
	function fileList($d,$x=null){
		if( $dl=dirScan($d) ){ $l=array(); foreach( $dl as $f ) if( is_file($d.'/'.$f) && ($x?preg_match('/\.'.$x.'$/',$f):1) ) $l[]=$f; return $l; }
	}
	
	

	/**
	 * Get the list of directory
	 *
	 * @param string $d directory
	 */
	function dirList($d){
		if( $dl=dirScan($d) ){ $l=array(); foreach($dl as $f)if(is_dir($d.'/'.$f))$l[]=$f; return $l; }
	} 
	
	
	/**
	 * File extension
	 * 
	 * @param string $file filename
	 */
	function fileExt( $file ){
		return end( (explode('.', $file)) );
	}



	/**
	 * Get the name without extension
	 * 
	 * @param string $f filename
	 */
	function fileName( $f ){
		
		if( ($f = basename($f) ) && ( $dot_pos = strrpos( $f , "." ) ) )
			return substr( $f, 0, $dot_pos );
	}


	/**
	 * Get directory of a filename
	 * 
	 * @param string $f filename
	 */
	function fileDir( $f ){
		return substr( $f, 0, strrpos( $f, "/" )+1 );
	}
	
	
	
	/**
	 * Delete dir and contents
	 * 
	 * @param string $d directory
	 */
	function dirDel($d) {
		if( $l=dirScan($d) ){ foreach($l as $f) if (is_dir($d."/".$f)) dirDel($d.'/'.$f);	else unlink($d."/".$f);	return rmdir($d); }
	}

	/**
	 * Copy all the content of a directory
	 * 
	 * @param string $s source directory
	 * @param string $d destination directory
	 */
	function dirCopy( $s, $d) {
		if (is_file($s)){
			copy($s, $d);
			chmod($d, fileperms($s) );
		}
		else{
			mkdir( $d, 0777 );
			if( $l=dirScan($s) ){ foreach( $l as $f ) dirCopy("$s/$f", "$d/$f"); }
		}
	} 


	
	/**
	 * Upload one file selected with $file. Use it when you pass only one file with a form.
	 * The file is saved into UPS_DIR, the name created as "md5(time()) . file_extension"
	 * it return the filename
	 * 
	 * @return string uploaded filename
	 */
	function uploadFile( $file ){
		if( $_FILES[$file]["tmp_name"] ){
			move_uploaded_file( $_FILES[$file]["tmp_name"], UPS_DIR . ( $filename = md5(time()).".".( strtolower( fileExt($_FILES[$file]['name'] ) ) ) ) );
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
	function uploadImage( $file, $thumb_prefix = null, $max_width = 128, $max_height = 128, $square = false ){
		if( $filename = uploadFile( $file ) ){
			//se voglio creare la thumb e $square=true, tento di creare la thumbnails
			if( $thumb_prefix && !imageResize( UPS_DIR . $filename,  UPS_DIR . $thumb_prefix . $filename, $max_width, $max_height, $square ) ){
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
	function imageResize( $source, $dest, $maxx = 100, $maxy = 100, $square = false, $quality = 70 ){

		switch( $ext = fileEXT( $source ) ){
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
//					UTILITY FUNCTIONS
//
//-------------------------------------------------------------
	

	/**
	 * Return true if $ip is a valid ip
	 */
	function isIp($ip){
	    return preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}^", $ip );
	}

	/**
	 * Return the array with all geolocation info of user selected by IP
	 */
	define( "IPINFODB_KEY", "acc71fd652705b79e5ffb586f51733e4ee3a22618ebddd7051a84998ed357cc0" );
	function ip2location( $ip = IP ){
		if( isIp( $ip ) )
			return json_decode( file_get_contents( "http://api.ipinfodb.com/v2/ip_query.php?key=".IPINFODB_KEY."&ip={$ip}&output=json&timezone=true" ) );
	}

	/**
	 * Convert byte to more readable format, like "1 KB" instead of "1024".
	 * cut_zero, remove the 0 after comma ex:  10,00 => 10      14,30 => 14,3
	 */
	function byteFormat( $size ){
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
	function moneyFormat( $number, $add_currency = false ){
		return ( $add_currency && CURRENCY_SIDE == 0 ? CURRENCY . " " : "" ) . number_format($number,2,DEC_POINT,THOUSANDS_SEP) . ( $add_currency && CURRENCY_SIDE == 1 ? " " . CURRENCY : "" );
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
	
	/**
	 * Useful for debug, print the variable $mixed and die
	 */
	function dump( $mixed, $exit = 1 ){
		echo "<pre>dump \n---------------------- \n\n" . print_r( $mixed, true ) . "\n----------------------<pre>";
		if( $exit ) exit;
	}

	/**
	 * Transform an object into an array
	 */
	function object_to_array($mixed) {
	    if(is_object($mixed)) $mixed = (array) $mixed;
	    if(is_array($mixed)) {
	        $new = array();
	        foreach($mixed as $key => $val) {
	            $key = preg_replace("/^\\0(.*)\\0/","",$key);
	            $new[$key] = object_to_array($val);
	        }
	    }
	    else $new = $mixed;
	    return $new;       
	}
		

?>