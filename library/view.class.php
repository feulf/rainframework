<?php

/**
 *	Rain Framework > View Class
 *	-----------------------------
 * 
 * 
 *	@author Federico Ulfo
 *	@copyright developed and mantained by the Rain Team: http://www.raintm.com
 *	@license Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *	@link http://www.rainframework.com
 *	@package RainFramework
 */



/**
 * Load and draw templates
 *
 */
class View{

	// -------------------------
	// 	CONFIGURATION 
	// -------------------------

	static 	$tpl_dir = null,
			$cache_dir = null,
			$base_url = null;

	// -------------------------

	// template object
	private $tpl_obj;

	
	
	/**
	 * Loads the template class
	 *
	 * @param string $template_class The template class that loads the template engine
	 */
	function View( $view_class = DEFAULT_VIEW_CLASS ){
		$view_class = $view_class . "_View";
		require_once APPLICATION_LIBRARY_DIR . 'view/' . $view_class . '.class.php';
		$this->view = new $view_class( self::$tpl_dir, self::$cache_dir, self::$base_url );
	}

	/**
	 * Assign variables to the template
	 *
	 */
	function assign( $variable, $value = null ){
		$this->view->assign( $variable, $value );
	}


	/**
	 * Draw the template
	 *
	 */
	function draw( $template, $return_string = false ){
		return $this->view->draw( $template, $return_string );
	}
	
	
	/**
	 * Return true if the template is cached
	 *
	 */
	function is_cached( $template, $expire_time = HOUR ){
		return $this->view->is_cached( $template, $expire_time );
	}
	
}




?>