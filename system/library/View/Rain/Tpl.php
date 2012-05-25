<?php

/**
 *  Rain\TPL
 *  --------
 *  Realized by Federico Ulfo & maintained by the Rain Team
 *  Distributed under GNU/LGPL 3 License
 *
 *  @version 3.0 Alpha
 */

namespace Rain;

class Tpl{

	// variables
	protected static	$config_check_sum	= null,
						$debug				= false,
						$tpl_dir			= "templates/",
						$cache_dir			= "cache/",
						$base_url			= null,
						$tpl_ext			= "html",
						$php_enabled		= false,
						$template_syntax	= "Rain",
						$path_replace		= true,
						$path_replace_list	= array( 'a', 'img', 'link', 'script', 'input' ),
						$black_list			= array( '\$this', 'raintpl::', 'self::', '_SESSION', '_POST', '_SERVER', '_ENV',  'eval', 'exec', 'unlink', 'rmdir' );


	/**
	 * Draw the template
	 */
	static function draw( $template_file_path, $variables = array() ){
		
		extract( $variables );
		ob_start();
		require_once self::_check_template( $template_file_path );
		return ob_get_clean();

	}

	/**
	 * Configure the template
	 */
	static function configure( $setting, $value = null ){
		
		if( is_array( $setting ) )
			foreach( $setting as $key => $value )
				self::configure( $key, $value );
		else if( property_exists( __CLASS__, $setting ) ){
			self::$$setting = $value;
			self::$config_check_sum .= $value; // take trace of all config
		}
	}

	static protected function _check_template( $template ){
		// set filename
		$template_name				= basename( $template );
		$template_basedir			= strpos($template,"/") ? dirname($template) . '/' : null;
		$template_directory			= self::$tpl_dir . $template_basedir;
		$template_filepath			= $template_directory . $template_name . '.' . self::$tpl_ext;
		$parsed_template_filepath	= self::$cache_dir . $template_name . "." . md5( $template_directory . self::$config_check_sum ) . '.rtpl.php';
		$class_name					= str_replace( array(".","/"), "_", $parsed_template_filepath );

		// if the template doesn't exsist throw an error
		if( !file_exists( $template_filepath ) ){
			$e = new RainTpl_NotFoundException( 'Template '. $template_name .' not found!' );
			throw $e->setTemplateFile($template_filepath);
		}

		// Compile the template if the original has been updated
		if( self::$debug  ||  !file_exists( $parsed_template_filepath )  ||  ( filemtime($parsed_template_filepath) < filemtime( $template_filepath ) ) ){

			// compile template
			$compiler_class = "TplCompile" . ucfirst( strtolower( self::$template_syntax ) );
			include_once $compiler_class . ".php";
			
			$compiler_class::configure( get_class_vars( __CLASS__ ) );
			$compiler_class::compileFile( $template_name, $template_basedir, $template_filepath, $parsed_template_filepath );

		}
		return $parsed_template_filepath;
	}

}