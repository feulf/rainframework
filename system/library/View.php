<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



/**
 * Load and draw templates
 *
 */
class View{

	// -------------------------
	// 	CONFIGURATION
	// -------------------------

	private static 	$tpl_dir = null,
                        $cache_dir = null,
                        $base_url = null,
                        $library_dir = LIBRARY_DIR,
                        $view_class_dir = "View/",
                        $view_class = "Raintpl_View";

	// -------------------------

	// template object
	private $tpl_obj,
                $view_obj;




	/**
	 * Loads the template class
	 *
	 * @param string $template_class The template class that loads the template engine
	 */
	function __construct(){
                require_once self::$library_dir . self::$view_class_dir . self::$view_class . '.php';
		$this->view_obj = new self::$view_class( self::$tpl_dir, self::$cache_dir, self::$base_url );
	}

	/**
	 * Assign variables to the template
	 *
	 */
	function assign( $variable, $value = null ){
		$this->view_obj->assign( $variable, $value );
	}


	/**
	 * Draw the template
	 *
	 */
	function draw( $template, $return_string = false ){
		return $this->view_obj->draw( $template, $return_string );
	}


	/**
	 * Return true if the template is cached
	 *
	 */
	function is_cached( $template, $expire_time = HOUR, $cache_id = null ){
		return $this->view_obj->is_cached( $template, $expire_time, $cache_id );
	}


	/**
	 * Configure the settings
	 *
	 */
	static function configure( $setting, $value ){
		if( is_array( $setting ) )
			foreach( $setting as $key => $value )
				$this->configure( $key, $value );
		else if( property_exists( __CLASS__, $setting ) )
			self::$$setting = $value;
	}


}




// -- end