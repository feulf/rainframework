<?php

/**
 *  Rain\TplCompileRain
 *  -------
 *  Realized by Federico Ulfo & maintained by the Rain Team
 *  Distributed under GNU/LGPL 3 License
 *
 *  @version 3.0 Alpha
 */

class TplCompileRain{

	// variables
	protected static	$debug,
						$tpl_dir,
						$cache_dir,
						$base_url,
						$php_enabled,
						$path_replace,
						$path_replace_list,
						$black_list;

	// configure
	static function configure( $setting = array() ){
		foreach( $setting as $key => $value ){
			if( property_exists( __CLASS__, $key ) ){
				self::$$key = $value;
			}
        }
	}
	
	/**
	 * Compile the file
	 */
	static function compileFile( $template_name, $template_basedir, $template_filepath, $parsed_template_filepath ){

		// open the template
		$fp = fopen( $template_filepath, "r" );

		// lock the file
		if( flock( $fp, LOCK_SH ) ){
			
			// read the file			
			$code = fread($fp, filesize( $template_filepath ) );
			
			//xml substitution
			$code = preg_replace( "/<\?xml(.*?)\?>/s", "##XML\\1XML##", $code );
			
			// disable php tag
			if( !self::$php_enabled )
				$code = str_replace( array("<?","?>"), array("&lt;?","?&gt;"), $code );

			//xml re-substitution
			$code = preg_replace_callback ( "/##XML(.*?)XML##/s", function( $match ){ 
																		return "<?php echo '<?xml ".stripslashes($match[1])." ?>'; ?>";
																  }, $code ); 

			$parsed_code = self::_compileTemplate( $code, $template_basedir, self::$debug, self::$tpl_dir, self::$cache_dir, self::$path_replace, self::$path_replace_list, self::$black_list );
			$parsed_code = "<?php if(!class_exists('Rain\Tpl')){exit;}?>" . $parsed_code;

			// fix the php-eating-newline-after-closing-tag-problem
			$parsed_code = str_replace( "?>\n", "?>\n\n", $parsed_code );

			// create directories
			if( !is_dir( self::$cache_dir ) )
				mkdir( self::$cache_dir, 0755, true );

			// check if the cache is writable
			if( !is_writable( self::$cache_dir ) )
				throw new RainTpl_Exception ('Cache directory ' . self::$cache_dir . 'doesn\'t have write permission. Set write permission or set RAINTPL_CHECK_TEMPLATE_UPDATE to false. More details on http://www.raintpl.com/Documentation/Documentation-for-PHP-developers/Configuration/');

			//write compiled file
			file_put_contents( $parsed_template_filepath, $parsed_code );

			// release the file lock
			return flock($fp, LOCK_UN);

		}

	}

	/**
	 * Compile template
	 * @access protected
	 */
	static function _compileTemplate( $code, $template_basedir ){

		//path replace (src of img, background and href of link)
		if( self::$path_replace )
			$code = self::path_replace( $code, $template_basedir );

		// tags
		$tags = array( '({loop.*?})',
                       '({\/loop})',
                       '({if.*?})',
                       '({elseif.*?})',
                       '({else})',
                       '({\/if})',
                       '({noparse})',
                       '({\/noparse})',
                       '({ignore})',
                       '({\/ignore})',
                       '({include.*?})',
					   '({\$.*?})',
					   '({#.*?})'
					  );

		//split the code with the tags regexp
		$code_split = preg_split( "/" . implode( "|", $tags ) . "/", $code, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		//compile the code
		return self::_parseCode( $code_split );

	}



	/**
	 * Compile the code
	 * @access protected
	 */
	static protected function _parseCode( $code_split ){

		//variables initialization
		$parsed_code = $comment_is_open = $ignore_is_open = NULL;
        $open_if = $loop_level = 0;

	 	//read all parsed code
	 	while( $html = array_shift( $code_split ) ){

	 		//close ignore tag
			if( !$comment_is_open && strpos( $html, '{/ignore}' ) !== FALSE )
	 			$ignore_is_open = FALSE;

	 		//code between tag ignore id deleted
	 		elseif( $ignore_is_open ){
	 			//ignore the code
	 		}

	 		//close no parse tag
			elseif( strpos( $html, '{/noparse}' ) !== FALSE )
	 			$comment_is_open = FALSE;

	 		//code between tag noparse is not compiled
	 		elseif( $comment_is_open )
 				$parsed_code .= $html;

	 		//ignore
			elseif( strpos( $html, '{ignore}' ) !== FALSE )
	 			$ignore_is_open = TRUE;

	 		//noparse
	 		elseif( strpos( $html, '{noparse}' ) !== FALSE )
	 			$comment_is_open = TRUE;

			//include tag
			elseif( preg_match( '/{include="([^"]*)"}/', $html, $matches ) ){

				//variables substitution
				$include_var = self::var_replace( $matches[ 1 ], $loop_level );
				

				//dynamic include
				$parsed_code .=		'<?php 
										use Rain\\TPL; 
										echo Rain\\TPL::draw( dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" ) . basename("'.$include_var.'"), $variables );
									?>';
				
			}

	 		//loop
			elseif( preg_match( '/{loop="\${0,1}([^"]*)"}/', $html, $matches ) ){

	 			//increase the loop counter
	 			$loop_level++;

				//replace the variable in the loop
				$var = self::var_replace( '$' . $matches[ 1 ], $loop_level-1 );

				//loop variables
				$counter = "\$counter$loop_level";       // count iteration
				$key	 = "\$key$loop_level";               // key
				$value	 = "\$value$loop_level";           // value

				//loop code
				$parsed_code .=  "<?php $counter=-1; if( isset($var) && is_array($var) && sizeof($var) ) foreach( $var as $key => $value ){ $counter++; ?>";

			}

			//close loop tag
			elseif( strpos( $html, '{/loop}' ) !== FALSE ) {

				//iterator
				$counter = "\$counter$loop_level";

				//decrease the loop counter
				$loop_level--;

				//close loop code
				$parsed_code .=  "<?php } ?>";

			}

			//if
			elseif( preg_match( '/{if(?: condition){0,1}="([^"]*)"}/', $html, $matches ) ){

				//increase open if counter (for intendation)
				$open_if++;

				//tag
				$tag = $matches[ 0 ];

				//condition attribute
				$condition = $matches[ 1 ];

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = self::var_replace( $condition, $loop_level );

				//if code
				$parsed_code .=   "<?php if( $parsed_condition ){ ?>";

			}

			//elseif
			elseif( preg_match( '/{elseif(?: condition){0,1}="([^"]*)"}/', $html, $matches ) ){

				//tag
				$tag = $matches[ 0 ];

				//condition attribute
				$condition = $matches[ 1 ];

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = self::var_replace( $condition, $loop_level );

				//elseif code
				$parsed_code .=   "<?php }elseif( $parsed_condition ){ ?>";
			}

			//else
			elseif( strpos( $html, '{else}' ) !== FALSE ) {

				//else code
				$parsed_code .=   '<?php }else{ ?>';

			}

			//close if tag
			elseif( strpos( $html, '{/if}' ) !== FALSE ) {

				//decrease if counter
				$open_if--;

				// close if code
				$parsed_code .=   '<?php } ?>';

			}

			//variables
			elseif( preg_match( '/{(\$.*?)}/', $html, $matches ) ){
				//variables substitution (es. {$title})
				$parsed_code .= "<?php echo " . self::var_replace( $matches[1], $loop_level ) . "; ?>";
			}
			
			//constants
			elseif( preg_match( '/{#(.*?)#{0,1}}/', $html, $matches ) ){
				$parsed_code .= "<?php echo " . self::con_replace( $matches[1], $loop_level ) . "; ?>";
			}

			// template info
			else{
				$parsed_code .= $html;
			}

		}

		if( $open_if > 0 ) {
			$e = new RainTpl_SyntaxException('Error! You need to close an {if} tag in ' . $template_filepath . ' template');
			throw $e->setTemplateFile($template_filepath);
		}

		return $parsed_code;

	}



	/**
	 * replace the path of image src, link href and a href.
	 * url => template_dir/url
	 * url# => url
	 * http://url => http://url
	 *
	 * @param string $html
	 * @return string html sostituito
	 */
	static protected function path_replace( $html, $template_basedir ){

		if( self::$path_replace ){

			// get the template base directory
			$template_directory = self::$base_url . self::$tpl_dir . $template_basedir;
			
			// reduce the path
			$path = preg_replace('/\w+\/\.\.\//', '', $template_directory );

			$exp = $sub = array();

			if( in_array( "img", self::$path_replace_list ) ){
				$exp = array( '/<img(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<img(.*?)src=(?:")([^"]+?)#(?:")/i', '/<img(.*?)src="(.*?)"/', '/<img(.*?)src=(?:\@)([^"]+?)(?:\@)/i' );
				$sub = array( '<img$1src=@$2://$3@', '<img$1src=@$2@', '<img$1src="' . $path . '$2"', '<img$1src="$2"' );
			}

			if( in_array( "script", self::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<script(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<script(.*?)src=(?:")([^"]+?)#(?:")/i', '/<script(.*?)src="(.*?)"/', '/<script(.*?)src=(?:\@)([^"]+?)(?:\@)/i' ) );
				$sub = array_merge( $sub , array( '<script$1src=@$2://$3@', '<script$1src=@$2@', '<script$1src="' . $path . '$2"', '<script$1src="$2"' ) );
			}

			if( in_array( "link", self::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<link(.*?)href=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<link(.*?)href=(?:")([^"]+?)#(?:")/i', '/<link(.*?)href="(.*?)"/', '/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i' ) );
				$sub = array_merge( $sub , array( '<link$1href=@$2://$3@', '<link$1href=@$2@' , '<link$1href="' . $path . '$2"', '<link$1href="$2"' ) );
			}

			if( in_array( "a", self::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<a(.*?)href=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<a(.*?)href="(.*?)"/', '/<a(.*?)href=(?:\@)([^"]+?)(?:\@)/i'  ) );
				$sub = array_merge( $sub , array( '<a$1href=@$2://$3@', '<a$1href="' . self::$base_url . '$2"', '<a$1href="$2"' ) );
			}

			if( in_array( "input", self::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<input(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<input(.*?)src=(?:")([^"]+?)#(?:")/i', '/<input(.*?)src="(.*?)"/', '/<input(.*?)src=(?:\@)([^"]+?)(?:\@)/i' ) );
				$sub = array_merge( $sub , array( '<input$1src=@$2://$3@', '<input$1src=@$2@', '<input$1src="' . $path . '$2"', '<input$1src="$2"' ) );
			}

			return preg_replace( $exp, $sub, $html );

		}
		else
			return $html;

	}



	static protected function var_replace( $html, $loop_level = NULL ){
		
		// change variable name if loop level
		if( $loop_level )
			$html = str_replace( array('$value','$key','$counter'), array('$value'.$loop_level,'$key'.$loop_level,'$counter'.$loop_level), $html );
		
		// if it is a variable
		if( preg_match_all('/(\$.*)/', $html, $matches ) ){

			// substitute . and [] with [" "]
			for( $i=0;$i<count($matches[1]);$i++ ){
				
				$rep = preg_replace( '/\[(\${0,1}[a-z_A-Z][a-z_A-Z0-9]*)\]/', '["$1"]', $matches[1][$i] );
				$rep = preg_replace( '/\.(\${0,1}[a-z_A-Z][a-z_A-Z0-9]*)/', '["$1"]', $rep );
				$html = str_replace( $matches[0][$i], $rep, $html );

			}

			// update modifier
			$html = self::modifier_replace( $html );

		}
		
		return $html;
		
	}

	static protected function con_replace( $html ){
		$html = self::modifier_replace( $html );
		return $html;
		
	}

	static protected function modifier_replace( $html ){

		if( $pos = strrpos( $html, "|" ) ){
			
			$explode = explode( ":", substr( $html, $pos+1 ) );
			$function = $explode[0];
			$params = isset( $explode[1] ) ? "," . $explode[1] : null;

			$html = $function . "(" . self::modifier_replace( substr( $html, 0, $pos ) ) . "$params)";
		}
		
		return $html;
	
	}



	/**
	 * Check if function is in black list (sandbox)
	 *
	 * @param string $code
	 * @param string $tag
	 */
	static protected function function_check( $code ){

		$preg = '#(\W|\s)' . implode( '(\W|\s)|(\W|\s)', self::$black_list ) . '(\W|\s)#';

		// check if the function is in the black list (or not in white list)
		if( count(self::$black_list) && preg_match( $preg, $code, $match ) ){

			// find the line of the error
			$line = 0;
			$rows=explode("\n",$this->tpl['source']);
			while( !strpos($rows[$line],$code) )
				$line++;

			// stop the execution of the script
			$e = new RainTpl_SyntaxException('Unallowed syntax in ' . $this->tpl['tpl_filename'] . ' template');
			throw $e->setTemplateFile($this->tpl['tpl_filename'])
				->setTag($code)
				->setTemplateLine($line);
		}

	}


}
