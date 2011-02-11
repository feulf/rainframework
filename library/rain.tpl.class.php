<?php

/**
 *  RainTPL easy template engine load HTML template.
 * 
 *  @version 2.6
 *  @author Federico Ulfo <rainelemental@gmail.com> | www.federicoulfo.it
 *  @copyright RainTPL is under GNU/LGPL 3 License
 *  @link http://www.raintpl.com
 *  @package RainFramework
 */




/**
 * Check template.
 * true: checks template update time, if changed it compile them
 * false: loads the compiled template. Set false if server doesn't have write permission for cache_directory.
 * 
 */
define( "RAINTPL_CHECK_TEMPLATE_UPDATE", true );




/**
 * Default cache expiration time (in seconds)
 * 
 */
define( "RAINTPL_CACHE_EXPIRE_TIME", 3600 );




/**
 * Load and draw templates
 *
 */
class RainTPL{


	// -------------------------
	// 	CONFIGURATION 
	// -------------------------

		static 	public 	$tpl_dir = "tpl/",		// template directory
						$cache_dir = "tmp/",	// template cache/compile directory
						$base_url = null,		// template base url (useful for absolute path eg. http://www.raintpl.com )
						$tpl_ext = "html";		// template extension. Set "php" to loads View

		// Path replace is a cool features that replace all relative paths of images (<img src="...">), stylesheet (<link href="...">), script (<script src="...">) and link (<a href="...">)
		static public $path_replace = true,	// set true if you want to use the path replace
					  $path_replace_list = array( 'a','img','link','script' ); // You can set what the path_replace method will replace - AVAIBLE OPTIONS: a, img, link, script

		// Black List - define the black list to disable variables or functions
		// is advised to leave always '\$this' into the black_list to avoid
		static public $black_list = array( '\$this', '_SESSION', '_SERVER', '_ENV', 'raintpl::', 'eval', 'exec', 'file_exists' );

	// -------------------------


	// -------------------------
	// 	RAINTPL VARIABLES
	// -------------------------
	
		public  $var = array();				// variables assigned to the templates
	
		private $tpl = array(),				// array of raintpl variables
			   	$static_cache = false;		// static cache enabled / disabled
	
	// -------------------------


	/**
	 * Assign variable
	 * eg. 	$t->assign('name','duck');
	 *
	 * @param mixed $variable_name Name of template variable or associative array name/value
	 * @param mixed $value value assigned to this variable. Not set if variable_name is an associative array
	 */

	function assign( $variable, $value = null ){

		if( is_array( $variable ) )
			$this->var += $variable;
		elseif( is_object( $variable ) )
			$this->var += (array) $variable;
		else
			$this->var[ $variable ] = $value;

	}



	/**
	 * Draw the template
	 * eg. 	$html = $tpl->draw( 'demo', TRUE ); // return template in string
	 * or 	$tpl->draw( $tpl_name ); // echo the template
	 *
	 * @param string $tpl_name  template to load
	 * @param boolean $return_string  true=return a string, false=echo the template
	 * @return string
	 */

	function draw( $tpl_name, $return_string = false ){

		// compile the template if necessary and set the template filepath
		$this->check_template( $tpl_name );

		//----------------------
		// load the template
		//----------------------
	
			ob_start();
			extract( $this->var );
			include $this->tpl['template_file'];
			$raintpl_contents = ob_get_contents();
			ob_end_clean();
		
		//----------------------



		//----------------------
		// save the static cache
		//----------------------

			if( $this->static_cache )
				file_put_contents( $this->tpl['static_cache_filename'], "<?php if(!class_exists('raintpl')){exit;}?>" . $raintpl_contents );
				

		//----------------------


		// unset tpl variable
		unset( $this->tpl );

		// return or print the template
		if( $return_string ) return $raintpl_contents; else echo $raintpl_contents;

	}
	




	/**
	 * If exists a valid cache for this template it returns the cache
	 *
	 * @param string $tpl_name Name of template (set the same of draw)
	 * @param int $expiration_time Set after how many seconds the cache expire and must be refreshed 
	 * @return string it return the HTML or null if the cache must be recreated
	 */

	function cache( $tpl_name, $expiration_time = RAINTPL_CACHE_EXPIRE_TIME ){

		// compile the template if necessary and set the template filepath
		$this->check_template( $tpl_name );

		// check if there's a valid cache
		if( !$this->tpl['tpl_has_changed'] && file_exists( $this->tpl['static_cache_filename'] ) && ( time() - filemtime( $this->tpl['static_cache_filename'] ) < $expiration_time ) )
			return substr( file_get_contents( $this->tpl['static_cache_filename'] ), 43 );
		else{
			//delete the cache of the selected template
			array_map( "unlink", glob( $this->tpl['static_cache_filename'] ) );
			$this->static_cache = true;
		}

	}





	/**
	 * Check the template and compile it if necessary
	 * Also set template filepath
	 *
	 * @param string $tpl_name name of the template
	 */
	private function check_template( $tpl_name ){

		// if already checked is not necessary to check again
		// optimized for avoid double checking when use cache method
		if( !isset($this->tpl['checked']) ){

			$this->tpl['tpl_has_changed'] 		= false;																		// template has changed
			$this->tpl['tpl_basename'] 			= basename( $tpl_name );														// template basename
			$this->tpl['tpl_basedir'] 			= strpos($tpl_name,"/") ? dirname($tpl_name) . '/' : null;						// template basedirectory
			$this->tpl['tpl_dir'] 				= raintpl::$tpl_dir . $this->tpl['tpl_basedir'];								// template directory

			// If template extension is php RainTPL loads Views (php templates) and is not necessary to compile the template
			if( raintpl::$tpl_ext == 'php' )
				$this->tpl['template_file']			= $this->tpl['tpl_dir'] . $this->tpl['tpl_basename'] . '.' . raintpl::$tpl_ext;		// template filename

			// set the template path and compile if necessary
			else{

				$this->tpl['tpl_filename'] 			= $this->tpl['tpl_dir'] . $this->tpl['tpl_basename'] . '.' . raintpl::$tpl_ext;		// template filename
				$this->tpl['cache_dir'] 			= raintpl::$cache_dir . $this->tpl['tpl_dir'];								// cache directory
				$this->tpl['template_file']			= $this->tpl['cache_filename']		= $this->tpl['cache_dir'] . $this->tpl['tpl_basename'] . '.php';				// cache filename
				$this->tpl['static_cache_filename'] = $this->tpl['cache_dir'] . $this->tpl['tpl_basename'] . '.s.php';				// static cache filename

				// if the template doesn't exists throw an error
				if( RAINTPL_CHECK_TEMPLATE_UPDATE && raintpl::$tpl_ext!='php' && !file_exists( $this->tpl['tpl_filename'] ) ){
					trigger_error( 'Template '.$this->tpl['tpl_basename'].' not found!' );
					return '<div style="background:#f8f8ff;border:1px solid #aaaaff;padding:10px;">Template <b>'.$this->tpl['tpl_basename'].'</b> not found</div>';
				}

				// file doesn't exsist, or the template was updated, Rain will compile the template
				if( RAINTPL_CHECK_TEMPLATE_UPDATE && raintpl::$tpl_ext!='php' && ( !file_exists( $this->tpl['cache_filename'] ) || filemtime($this->tpl['cache_filename']) < filemtime($this->tpl['tpl_filename']) ) ){
					$this->compileFile( $this->tpl['tpl_basedir'], $this->tpl['tpl_filename'], $this->tpl['cache_dir'], $this->tpl['cache_filename'] );
					$this->tpl['tpl_has_changed'] = true;
				}

			}

			// template and variable was checked
			$this->tpl['checked'] = true;
		}
	}
	

	

	

	/**
	 * Compile and write the compiled template file
	 *
	 */

	private function compileFile( $tpl_basedir, $tpl_filename, $cache_dir, $cache_filename ){

		//read template file
		$this->tpl['source'] = $template_code = file_get_contents( $tpl_filename );

		//xml substitution
		$template_code = preg_replace( "/\<\?xml(.*?)\?\>/", "##XML\\1XML##", $template_code );

		//disable php tag
		$template_code = preg_replace( array("/\<\?/","/\?\>/"), array("&lt;?","?&gt;"), $template_code );

		//xml re-substitution
		$template_code = preg_replace( "/\#\#XML(.*?)XML\#\#/", "<?php echo '<?xml' . stripslashes('\\1') . '?>'; ?>", $template_code );

		//compile template
		$template_compiled = "<?php if(!class_exists('raintpl')){exit;}?>" . $this->compileTemplate( $template_code, $tpl_basedir );

		// create directories
		if( !is_dir( $cache_dir ) )
			mkdir( $cache_dir, 0755, true );

		if( !is_writable( $cache_dir ) )
			die( "Cache directory <b>$cache_dir</b> doesn't have write permission. Set write permission or set RAINTPL_CHECK_TEMPLATE_UPDATE to false. More details on <a target=_blank href=http://www.raintpl.com/Documentation/Documentation-for-PHP-developers/Configuration/>Configuration</a>");

		//write compiled file
		file_put_contents( $cache_filename, $template_compiled );			
	}



	/**
	 * Compile template
	 * @access private
	 */
	private function compileTemplate( $template_code, $tpl_basedir ){

		//tag list
		$tag_regexp = '/(\{loop(?: name){0,1}="(?:\$){0,1}(?:.*?)"\})|(\{\/loop\})|(\{if(?: condition){0,1}="(?:.*?)"\})|(\{elseif(?: condition){0,1}="(?:.*?)"\})|(\{else\})|(\{\/if\})|(\{function="(?:.*?)"\})|(\{noparse\})|(\{\/noparse\})|(\{ignore\})|(\{\/ignore\})|(\{include="(?:.*?)"(?: cache="(?:.*?)")?\})/';

		//split the code with the tags regexp
		$template_code = preg_split ( $tag_regexp, $template_code, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		//path replace (src of img, background and href of link)
		$template_code = $this->path_replace( $template_code, $tpl_basedir );

		//compile the code
		$compiled_code = $this->compileCode( $template_code );

		//return the compiled code
		return $compiled_code;

	}



	/**
	 * Compile the code
	 * @access private
	 */
	private function compileCode( $parsed_code ){

		//variables initialization
		$parent_loop[ $level = 0 ] = $loop_name = $loop_loopelse_open = $compiled_code = $compiled_return_code = $open_if = $comment_is_open = $ignore_is_open = null;

	 	//read all parsed code
	 	while( $html = array_shift( $parsed_code ) ){

	 		//close ignore tag
	 		if( !$comment_is_open && preg_match( '/\{\/ignore\}/', $html ) )
	 			$ignore_is_open = false;

	 		//code between tag ignore id deleted
	 		elseif( $ignore_is_open ){
	 			//ignore the code
	 		}

	 		//close no parse tag
	 		elseif( preg_match( '/\{\/noparse\}/', $html ) )
	 			$comment_is_open = false;	

	 		//code between tag noparse is not compiled
	 		elseif( $comment_is_open )
 				$compiled_code .= $html;

	 		//ignore
	 		elseif( preg_match( '/\{ignore\}/', $html ) )
	 			$ignore_is_open = true;

	 		//noparse
	 		elseif( preg_match( '/\{noparse\}/', $html ) )
	 			$comment_is_open = true;

			//include tag
			elseif( preg_match( '/(?:\{include="(.*?)"(?: cache="(.*?)"){0,1}\})/', $html, $code ) ){

				//variables substitution
				$include_var = $this->var_replace( $code[ 1 ], $left_delimiter = null, $right_delimiter = null, $php_left_delimiter = '".' , $php_right_delimiter = '."', $this_loop_name = $parent_loop[ $level ] );
				
				// if the cache is active
				if( isset($code[ 2 ]) )
					//dynamic include
					$compiled_code .= '<?php $tpl = new RainTPL;' .
								 'if( $cache = $tpl->cache( $cache_filename = basename("'.$include_var.'") ) )' .
								 '	echo $cache;' .
								 'else{ ' .
								 '$tpl_dir_temp = raintpl::$tpl_dir;' .
								 '$tpl->assign( $this->var );' .
								 'raintpl::$tpl_dir .= dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" );' .
								 ( !$this_loop_name ? null : '$tpl->assign( "key", $key'.$this_loop_name.' ); $tpl->assign( "value", $value'.$this_loop_name.' );' ).
								 '$tpl->draw( $cache_filename );'.
								 'raintpl::$tpl_dir = $tpl_dir_temp;' . 
								 '}' .
								 '?>';
				else
					//dynamic include
					$compiled_code .= '<?php $tpl = new RainTPL;' .
								 '$tpl_dir_temp = raintpl::$tpl_dir;' .
								 '$tpl->assign( $this->var );' .
								 'raintpl::$tpl_dir .= dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" );' .
								 ( !$this_loop_name ? null : '$tpl->assign( "key", $key'.$this_loop_name.' ); $tpl->assign( "value", $value'.$this_loop_name.' );' ).
								 '$tpl->draw( basename("'.$include_var.'") );'.
								 'raintpl::$tpl_dir = $tpl_dir_temp;' . 
								 '?>';
								 
			}

	 		//loop
	 		elseif( preg_match( '/\{loop(?: name){0,1}="(?:\$){0,1}(.*?)"\}/', $html, $code ) ){
	 			
	 			//increase the loop counter
	 			$level++;
	 			
	 			//name of this loop
				$parent_loop[ $level ] = $level;

				//replace the variable in the loop
				$var = $this->var_replace( '$' . $code[ 1 ], $tag_left_delimiter=null, $tag_right_delimiter=null, $php_left_delimiter=null, $php_right_delimiter=null, $level-1 );

				//loop variables
				$counter = "\$counter$level";	// count iteration
				$key = "\$key$level";			// key
				$value = "\$value$level";		// value
				
				//loop code
				$compiled_code .=  "<?php $counter=-1; if( isset($var) && is_array($var) && sizeof($var) ) foreach( $var as $key => $value ){ $counter++; ?>";

			}

			//close loop tag
			elseif( preg_match( '/\{\/loop\}/', $html ) ){

				//iterator
				$counter = "\$counter$level";

				//decrease the loop counter
				$level--;

				//close loop code
				$compiled_code .=  "<?php } ?>";
				
			}

			//if
			elseif( preg_match( '/\{if(?: condition){0,1}="(.*?)"\}/', $html, $code ) ){
				
				//increase open if counter (for intendation)
				$open_if++;

				//tag
				$tag = $code[ 0 ];

				//condition attribute
				$condition = $code[ 1 ];
				
				// check if there's any function disabled by black_list
				$this->function_check( $tag );

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = $this->var_replace( $condition, $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] );				

				//if code
				$compiled_code .=   "<?php if( $parsed_condition ){ ?>";
			}

			//elseif
			elseif( preg_match( '/\{elseif(?: condition){0,1}="(.*?)"\}/', $html, $code ) ){

				//increase open if counter (for intendation)
				$open_if++;

				//tag
				$tag = $code[ 0 ];

				//condition attribute
				$condition = $code[ 1 ];

				// check if there's any function disabled by black_list
				$this->function_check( $tag );

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = $this->var_replace( $condition, $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] );				

				//elseif code
				$compiled_code .=   "<?php }elseif( $parsed_condition ){ ?>";
			}

			//else
			elseif( preg_match( '/\{else\}/', $html ) ){

				//else code
				$compiled_code .=   '<?php }else{ ?>';

			}
						
			//close if tag
			elseif( preg_match( '/\{\/if}/', $html ) ){
				
				//decrease if counter
				$open_if--;
				
				// close if code 
				$compiled_code .=   '<?php } ?>';

			}

			//function
			elseif( preg_match( '/\{function="(.*?)(\((.*?)\)){0,1}"\}/', $html, $code ) ){

				//tag
				$tag = $code[ 0 ];

				//function
				$function = $code[ 1 ];
				
				// check if there's any function disabled by black_list
				$this->function_check( $tag );

				//parse the parameters
				$parsed_param = isset( $code[2] ) ? $this->var_replace( $code[2], $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $parent_loop[ $level ] ) : '()';

				//if code
				$compiled_code .=   "<?php echo {$function}{$parsed_param}; ?>";
			}

			//all html code
			else{

				//variables substitution (es. {$title})
				$compiled_code .= $this->var_replace( $html, $left_delimiter = '\{', $right_delimiter = '\}', $php_left_delimiter = '<?php ', $php_right_delimiter = ';?>', $parent_loop[ $level ], $echo = true );

			}
		}

		return $compiled_code;
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
	private function path_replace( $html, $tpl_basedir ){
		
		if( raintpl::$path_replace ){

			$exp = $sub = array();

			if( in_array( "img", raintpl::$path_replace_list ) ){
				$exp = array( '/<img(.*?)src=(?:")http\:\/\/([^"]+?)(?:")/i', '/<img(.*?)src=(?:")([^"]+?)#(?:")/i', '/<img(.*?)src="(.*?)"/', '/<img(.*?)src=(?:\@)([^"]+?)(?:\@)/i' );
				$sub = array( '<img$1src=@http://$2@', '<img$1src=@$2@', '<img$1src="' . raintpl::$base_url . raintpl::$tpl_dir . $tpl_basedir . '$2"', '<img$1src="$2"' );
			}
			
			if( in_array( "script", raintpl::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<script(.*?)src=(?:")http\:\/\/([^"]+?)(?:")/i', '/<script(.*?)src=(?:")([^"]+?)#(?:")/i', '/<script(.*?)src="(.*?)"/', '/<script(.*?)src=(?:\@)([^"]+?)(?:\@)/i' ) );
				$sub = array_merge( $sub , array( '<script$1src=@http://$2@', '<script$1src=@$2@', '<script$1src="' . raintpl::$base_url . raintpl::$tpl_dir . $tpl_basedir . '$2"', '<script$1src="$2"' ) );
			}
			
			if( in_array( "link", raintpl::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<link(.*?)href=(?:")http\:\/\/([^"]+?)(?:")/i', '/<link(.*?)href=(?:")([^"]+?)#(?:")/i', '/<link(.*?)href="(.*?)"/', '/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i' ) );
				$sub = array_merge( $sub , array( '<link$1href=@http://$2@', '<link$1href=@$2@' , '<link$1href="' . raintpl::$base_url . raintpl::$tpl_dir . $tpl_basedir . '$2"', '<link$1href="$2"' ) );
			}
			
			if( in_array( "a", raintpl::$path_replace_list ) ){
				$exp = array_merge( $exp , array( '/<a(.*?)href=(?:")http\:\/\/([^"]+?)(?:")/i', '/<a(.*?)href="(.*?)"/' ) );
				$sub = array_merge( $sub , array( '<a$1href=@http://$2@',  '<a$1href="' . raintpl::$base_url . '$2"' ) );
			}

			/*
			$exp = array( '/<(img|script)(.*?)src=(?:")http\:\/\/([^"]+?)(?:")/i', '/<(img|script)(.*?)src=(?:")([^"]+?)#(?:")/i', '/<(img|script)(.*?)src="(.*?)"/', '/<(img|script)(.*?)src=(?:\@)([^"]+?)(?:\@)/i',
						  '/<link(.*?)href=(?:")http\:\/\/([^"]+?)(?:")/i', '/<link(.*?)href=(?:")([^"]+?)#(?:")/i', '/<link(.*?)href="(.*?)"/', '/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i', 
						  '/<a(.*?)href=(?:")http\:\/\/([^"]+?)(?:")/i', '/<a(.*?)href="(.*?)"/' );
	
			$sub = array( 	'<$1$2src=@http://$3@', '<$1$2src=@$3@', '<$1$2src="' . raintpl::$base_url . raintpl::$tpl_dir . $tpl_basedir . '$3"', '<$1$2src="$3"', 
							'<link$1href=@http://$2@', '<link$1href=@$2@' , '<link$1href="' . raintpl::$base_url . raintpl::$tpl_dir . $tpl_basedir . '$2"', '<link$1href="$2"',
							'<a$1href=@http://$2@',  '<a$1href="' . raintpl::$base_url . '$2"' );
			*/
			return preg_replace( $exp, $sub, $html );
			
		}
		else
			return $html;

	}



	



	
	/**
	 * Variable substitution
	 *
	 * @param string $html Html code
	 * @param string $tag_left_delimiter default {
	 * @param string $tag_right_delimiter default }
	 * @param string $php_left_delimiter default <?php=
	 * @param string $php_right_delimiter  default ;?>
	 * @param string $loop_name Loop name
	 * @param string $echo if is true make the variable echo
	 * @return string Replaced code
	 */
	function var_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_name = null, $echo = null ){


		// const
		$html = preg_replace( '/\{\#(\w+)\#\}/', $php_left_delimiter . ( $echo ? " echo " : null ) . '\\1' . $php_right_delimiter, $html );

		
		//all variables
		preg_match_all( '/' . $tag_left_delimiter . '\$(\w+(?:\.\${0,1}(?:\w+))*(?:\[\${0,1}(?:\w+)\])*(?:\-\>\${0,1}(?:\w+))*)(.*?)' . $tag_right_delimiter . '/', $html, $matches );

		$n = sizeof( $matches[ 0 ] );
		for( $i = 0; $i < $n; $i++ ){

			//complete tag ex: {$news.title|substr:0,100}
			$tag = $matches[ 0 ][ $i ];

			//variable name ex: news.title
			$var = $matches[ 1 ][ $i ];
			
			//function and parameters associate to the variable ex: substr:0,100
			$extra_var = $matches[ 2 ][ $i ];
			
			// check if there's any function disabled by black_list
			$this->function_check( $tag );
			
			$extra_var = $this->var_replace( $extra_var, null, null, null, null, $loop_name );
			
			// check if there's an operator = in the variable tags, if there's this is an initialization so it will not output any value
			$is_init_variable = preg_match( "/^(\s*?)\=[^=](.*?)$/", $extra_var );
			
			//function associate to variable
			$function_var = ( $extra_var and $extra_var[0] == '|') ? substr( $extra_var, 1 ) : null;
			
			//variable path split array (ex. $news.title o $news[title]) or object (ex. $news->title)
			$temp = preg_split( "/\.|\[|\-\>/", $var );
			
			//variable name
			$var_name = $temp[ 0 ];
			
			//variable path
			$variable_path = substr( $var, strlen( $var_name ) );
			
			//parentesis transform [ e ] in [" e in "]
			$variable_path = str_replace( '[', '["', $variable_path );
			$variable_path = str_replace( ']', '"]', $variable_path );
			
			//transform .$variable in ["$variable"]
			$variable_path = preg_replace('/\.\$(\w+)/', '["$\\1"]', $variable_path );
			
			//transform [variable] in ["variable"]
			$variable_path = preg_replace('/\.(\w+)/', '["\\1"]', $variable_path );

			//if there's a function
			if( $function_var ){
				
				//split function by function_name and parameters (ex substr:0,100)
				$function_split = explode( ':', $function_var, 2 );
				
				//function name
				$function = $function_split[ 0 ];
				
				//function parameters
				$params = ( isset( $function_split[ 1 ] ) ) ? $function_split[ 1 ] : null;

			}
			else
				$function = $params = null;
			
			//if it is inside a loop
			if( $loop_name ){
				//verify the variable name
				if( $var_name == 'key' )
					$php_var = '$key' . $loop_name;
				elseif( $var_name == 'value' )
					$php_var = '$value' . $loop_name . $variable_path;
				elseif( $var_name == 'counter' )
					$php_var = '$counter' . $loop_name;
				else
					$php_var = "\$" . $var_name . $variable_path;
			}else
				$php_var = "\$" . $var_name . $variable_path;

			// compile the variable for php
			if( isset( $function ) )
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $php_var, $params ) )" : "$function( $php_var )" ) . $php_right_delimiter;
			else
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . $php_var . $extra_var . $php_right_delimiter;

			$html = str_replace( $tag, $php_var, $html );

		}
		
		return $html;
	}
	
	
	
	/**
	 * Check if function is in black list (sandbox)
	 *
	 * @param string $code
	 * @param string $tag
	 */
	private function function_check( $code ){

		$preg = '#(\W|\s)' . implode( '(\W|\s)|(\W|\s)', raintpl::$black_list ) . '(\W|\s)#';

		// check if the function is in the black list (or not in white list)
		if( count(raintpl::$black_list) && preg_match( $preg, $code, $match ) ){

			// find the line of the error
			$line = 0;
			$rows=explode("\n",$this->tpl['source']);
			while( !strpos($rows[$line],$code) )
				$line++;

			// draw the error line
			$error = str_replace( array('<','>'), array( '&lt;','&gt;' ), array($code,$rows[$line]) );
			$error = str_replace( $code, "<font color=red>$code</font>", $rows[$line] );

			// debug the error and stop the execution of the script
			die( "<div>RainTPL Sandbox Error in template <b>{$this->tpl['tpl_filename']}</b> at line $line : <i>$error</i></b>" );
		}
		
	}


}




?>