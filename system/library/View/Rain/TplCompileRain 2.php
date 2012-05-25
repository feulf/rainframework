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
						$base_url,
						$path_replace,
						$path_replace_list,
						$black_list;

	static function conf( $setting = array() ){
		foreach( $setting as $key => $value ){
			if( property_exists( __CLASS__, $key ) ){
				self::$$key = $value;
			}
        }
	}
	
	/**
	 * Compile template
	 * @access protected
	 */
	static function _compileTemplate( $code, $template_basedir ){

		//tag list
		$tag_regexp = array( 'loop'         => '(\{loop(?: name){0,1}="\${0,1}[^"]*"\})',
                             'loop_close'   => '(\{\/loop\})',
                             'if'           => '(\{if(?: condition){0,1}="[^"]*"\})',
                             'elseif'       => '(\{elseif(?: condition){0,1}="[^"]*"\})',
                             'else'         => '(\{else\})',
                             'if_close'     => '(\{\/if\})',
                             'function'     => '(\{function="[^"]*"\})',
                             'noparse'      => '(\{noparse\})',
                             'noparse_close'=> '(\{\/noparse\})',
                             'ignore'       => '(\{ignore\})',
                             'ignore_close'	=> '(\{\/ignore\})',
                             'include'      => '(\{include="[^"]*"\})',
                             'template_info'=> '(\{\$template_info\})',
                             'function'		=> '(\{function="(\w*?)(?:.*?)"\})'
							);

		$tag_regexp = "/" . join( "|", $tag_regexp ) . "/";

		//split the code with the tags regexp
		$code = preg_split ( $tag_regexp, $code, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

		//path replace (src of img, background and href of link)
		$code = self::path_replace( $code, $template_basedir );

		//compile the code
		$compiled_code = self::compileCode( $code );

		//return the compiled code
		return $compiled_code;

	}



	/**
	 * Compile the code
	 * @access protected
	 */
	protected function compileCode( $parsed_code ){

		//variables initialization
		$compiled_code = $open_if = $comment_is_open = $ignore_is_open = null;
        $loop_level = 0;

	 	//read all parsed code
	 	while( $html = array_shift( $parsed_code ) ){

	 		//close ignore tag
			if( !$comment_is_open && strpos( $html, '{/ignore}' ) !== FALSE )
	 			$ignore_is_open = false;

	 		//code between tag ignore id deleted
	 		elseif( $ignore_is_open ){
	 			//ignore the code
	 		}

	 		//close no parse tag
			elseif( strpos( $html, '{/noparse}' ) !== FALSE )
	 			$comment_is_open = false;

	 		//code between tag noparse is not compiled
	 		elseif( $comment_is_open )
 				$compiled_code .= $html;

	 		//ignore
			elseif( strpos( $html, '{ignore}' ) !== FALSE )
	 			$ignore_is_open = true;

	 		//noparse
	 		elseif( strpos( $html, '{noparse}' ) !== FALSE )
	 			$comment_is_open = true;

			//include tag
			elseif( preg_match( '/\{include="([^"]*)"\}/', $html, $code ) ){

				//variables substitution
				$include_var = self::var_replace( $code[ 1 ], $left_delimiter = null, $right_delimiter = null, $php_left_delimiter = '".' , $php_right_delimiter = '."', $loop_level );


				//dynamic include
				$compiled_code .= '<?php $tpl = new RainTPL;' .
							 '$template_directory_temp = self::$tpl_dir;' .
							 '$tpl->assign( $variables );' .
							 ( !$loop_level ? null : '$tpl->assign( "key", $key'.$loop_level.' ); $tpl->assign( "value", $value'.$loop_level.' );' ).
							 '$tpl->draw( dirname("'.$include_var.'") . ( substr("'.$include_var.'",-1,1) != "/" ? "/" : "" ) . basename("'.$include_var.'") );'.
							 '?>';

			}

	 		//loop
			elseif( preg_match( '/\{loop(?: name){0,1}="\${0,1}([^"]*)"\}/', $html, $code ) ){

	 			//increase the loop counter
	 			$loop_level++;

				//replace the variable in the loop
				$var = self::var_replace( '$' . $code[ 1 ], $tag_left_delimiter=null, $tag_right_delimiter=null, $php_left_delimiter=null, $php_right_delimiter=null, $loop_level-1 );

				//loop variables
				$counter = "\$counter$loop_level";       // count iteration
				$key = "\$key$loop_level";               // key
				$value = "\$value$loop_level";           // value

				//loop code
				$compiled_code .=  "<?php $counter=-1; if( isset($var) && is_array($var) && sizeof($var) ) foreach( $var as $key => $value ){ $counter++; ?>";

			}

			//close loop tag
			elseif( strpos( $html, '{/loop}' ) !== FALSE ) {

				//iterator
				$counter = "\$counter$loop_level";

				//decrease the loop counter
				$loop_level--;

				//close loop code
				$compiled_code .=  "<?php } ?>";

			}

			//if
			elseif( preg_match( '/\{if(?: condition){0,1}="([^"]*)"\}/', $html, $code ) ){

				//increase open if counter (for intendation)
				$open_if++;

				//tag
				$tag = $code[ 0 ];

				//condition attribute
				$condition = $code[ 1 ];

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = self::var_replace( $condition, $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $loop_level );

				//if code
				$compiled_code .=   "<?php if( $parsed_condition ){ ?>";

			}

			//elseif
			elseif( preg_match( '/\{elseif(?: condition){0,1}="([^"]*)"\}/', $html, $code ) ){

				//tag
				$tag = $code[ 0 ];

				//condition attribute
				$condition = $code[ 1 ];

				//variable substitution into condition (no delimiter into the condition)
				$parsed_condition = self::var_replace( $condition, $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $loop_level );

				//elseif code
				$compiled_code .=   "<?php }elseif( $parsed_condition ){ ?>";
			}

			//else
			elseif( strpos( $html, '{else}' ) !== FALSE ) {

				//else code
				$compiled_code .=   '<?php }else{ ?>';

			}

			//close if tag
			elseif( strpos( $html, '{/if}' ) !== FALSE ) {

				//decrease if counter
				$open_if--;

				// close if code
				$compiled_code .=   '<?php } ?>';

			}

			//function
			elseif( preg_match( '/\{function="(\w*)(.*?)"\}/', $html, $code ) ){

				//tag
				$tag = $code[ 0 ];

				//function
				$function = $code[ 1 ];

				if( empty( $code[ 2 ] ) )
					$parsed_function = $function . "()";
				else
					// parse the function
					$parsed_function = $function . self::var_replace( $code[ 2 ], $tag_left_delimiter = null, $tag_right_delimiter = null, $php_left_delimiter = null, $php_right_delimiter = null, $loop_level );
				
				//if code
				$compiled_code .=   "<?php echo $parsed_function; ?>";
			}

			// show all vars
			elseif ( strpos( $html, '{$template_info}' ) !== FALSE ) {

				//tag
				$tag  = '{$template_info}';

				//if code
				$compiled_code .=   '<?php echo "<pre>"; print_r( self::var ); echo "</pre>"; ?>';
			}


			//all html code
			else{

				//variables substitution (es. {$title})
				$html = self::var_replace( $html, $left_delimiter = '\{', $right_delimiter = '\}', $php_left_delimiter = '<?php ', $php_right_delimiter = ';?>', $loop_level, $echo = true );
				//const substitution (es. {#CONST#})
				$html = self::const_replace( $html, $left_delimiter = '\{', $right_delimiter = '\}', $php_left_delimiter = '<?php ', $php_right_delimiter = ';?>', $loop_level, $echo = true );
				//functions substitution (es. {"string"|functions})
				$compiled_code .= self::func_replace( $html, $left_delimiter = '\{', $right_delimiter = '\}', $php_left_delimiter = '<?php ', $php_right_delimiter = ';?>', $loop_level, $echo = true );
			}
		}

		if( $open_if > 0 ) {
			$e = new RainTpl_SyntaxException('Error! You need to close an {if} tag in ' . $template_filepath . ' template');
			throw $e->setTemplateFile($template_filepath);
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
	protected function path_replace( $html, $template_basedir ){

		if( self::$path_replace ){

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





	// replace const
	function const_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_level = null, $echo = null ){
		// const
		return preg_replace( '/\{\#(\w+)\#{0,1}\}/', $php_left_delimiter . ( $echo ? " echo " : null ) . '\\1' . $php_right_delimiter, $html );
	}



	// replace functions/modifiers on constants and strings
	function func_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_level = null, $echo = null ){

		preg_match_all( '/' . '\{\#{0,1}(\"{0,1}.*?\"{0,1})(\|\w.*?)\#{0,1}\}' . '/', $html, $matches );

		for( $i=0, $n=count($matches[0]); $i<$n; $i++ ){

			//complete tag ex: {$news.title|substr:0,100}
			$tag = $matches[ 0 ][ $i ];

			//variable name ex: news.title
			$var = $matches[ 1 ][ $i ];

			//function and parameters associate to the variable ex: substr:0,100
			$extra_var = $matches[ 2 ][ $i ];

			// check if there's any function disabled by black_list
			self::function_check( $tag );

			$extra_var = self::var_replace( $extra_var, null, null, null, null, $loop_level );
            

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
                
                // check if there's a function or a static method and separate, function by parameters
				$function_var = str_replace("::", "@double_dot@", $function_var );

                // get the position of the first :
                if( $dot_position = strpos( $function_var, ":" ) ){

                    // get the function and the parameters
                    $function = substr( $function_var, 0, $dot_position );
                    $params = substr( $function_var, $dot_position+1 );

                }
                else{

                    //get the function
                    $function = str_replace( "@double_dot@", "::", $function_var );
                    $params = null;

                }

                // replace back the @double_dot@ with ::
                $function = str_replace( "@double_dot@", "::", $function );
                $params = str_replace( "@double_dot@", "::", $params );


			}
			else
				$function = $params = null;

			$php_var = $var_name . $variable_path;

			// compile the variable for php
			if( isset( $function ) ){
				if( $php_var )
					$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $php_var, $params ) )" : "$function( $php_var )" ) . $php_right_delimiter;
				else
					$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $params ) )" : "$function()" ) . $php_right_delimiter;
			}
			else
				$php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . $php_var . $extra_var . $php_right_delimiter;

			$html = str_replace( $tag, $php_var, $html );

		}

		return $html;

	}



	function var_replace( $html, $tag_left_delimiter, $tag_right_delimiter, $php_left_delimiter = null, $php_right_delimiter = null, $loop_level = null, $echo = null ){

		//all variables
		if( preg_match_all( '/' . $tag_left_delimiter . '\$(\w+(?:\.\${0,1}[A-Za-z0-9_]+)*(?:(?:\[\${0,1}[A-Za-z0-9_]+\])|(?:\-\>\${0,1}[A-Za-z0-9_]+))*)(.*?)' . $tag_right_delimiter . '/', $html, $matches ) ){

                    for( $parsed=array(), $i=0, $n=count($matches[0]); $i<$n; $i++ )
                        $parsed[$matches[0][$i]] = array('var'=>$matches[1][$i],'extra_var'=>$matches[2][$i]);

                    foreach( $parsed as $tag => $array ){

                            //variable name ex: news.title
                            $var = $array['var'];

                            //function and parameters associate to the variable ex: substr:0,100
                            $extra_var = $array['extra_var'];

                            // check if there's any function disabled by black_list
                            self::function_check( $tag );

                            $extra_var = self::var_replace( $extra_var, null, null, null, null, $loop_level );

                            // check if there's an operator = in the variable tags, if there's this is an initialization so it will not output any value
                            $is_init_variable = preg_match( "/^[a-z_A-Z\.\[\](\-\>)]*=[^=]*$/", $extra_var );
                            
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

                            //transform .$variable in ["$variable"] and .variable in ["variable"]
                            $variable_path = preg_replace('/\.(\${0,1}\w+)/', '["\\1"]', $variable_path );
                            
                            // if is an assignment also assign the variable to self::var['value']
                            if( $is_init_variable )
                                $extra_var = "=\$variables['{$var_name}']{$variable_path}" . $extra_var;

                                

                            //if there's a function
                            if( $function_var ){
                                
                                    // check if there's a function or a static method and separate, function by parameters
                                    $function_var = str_replace("::", "@double_dot@", $function_var );


                                    // get the position of the first :
                                    if( $dot_position = strpos( $function_var, ":" ) ){

                                        // get the function and the parameters
                                        $function = substr( $function_var, 0, $dot_position );
                                        $params = substr( $function_var, $dot_position+1 );

                                    }
                                    else{

                                        //get the function
                                        $function = str_replace( "@double_dot@", "::", $function_var );
                                        $params = null;

                                    }

                                    // replace back the @double_dot@ with ::
                                    $function = str_replace( "@double_dot@", "::", $function );
                                    $params = str_replace( "@double_dot@", "::", $params );
                            }
                            else
                                    $function = $params = null;

                            //if it is inside a loop
                            if( $loop_level ){
                                    //verify the variable name
                                    if( $var_name == 'key' )
                                            $php_var = '$key' . $loop_level;
                                    elseif( $var_name == 'value' )
                                            $php_var = '$value' . $loop_level . $variable_path;
                                    elseif( $var_name == 'counter' )
                                            $php_var = '$counter' . $loop_level;
                                    else
                                            $php_var = '$' . $var_name . $variable_path;
                            }else
                                    $php_var = '$' . $var_name . $variable_path;
                            
                            // compile the variable for php
                            if( isset( $function ) )
                                    $php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . ( $params ? "( $function( $php_var, $params ) )" : "$function( $php_var )" ) . $php_right_delimiter;
                            else
                                    $php_var = $php_left_delimiter . ( !$is_init_variable && $echo ? 'echo ' : null ) . $php_var . $extra_var . $php_right_delimiter;
                            
                            $html = str_replace( $tag, $php_var, $html );


                    }
                }

		return $html;
	}
	
	

	/**
	 * Check if function is in black list (sandbox)
	 *
	 * @param string $code
	 * @param string $tag
	 */
	protected function function_check( $code ){

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