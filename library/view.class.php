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
	function View( $template_class = DEFAULT_VIEW_CLASS ){
		$template_class .= '_view';
		require_once LIBRARY_DIR . 'view/' . $template_class . '.class.php';
		$this->tpl_obj = new $template_class( self::$tpl_dir, self::$cache_dir, self::$base_url );
	}

	function assign( $variable, $value = null ){
		$this->tpl_obj->assign( $variable, $value );
	}

	function draw( $template, $return_string = false ){
		return $this->tpl_obj->draw( $template, $return_string );
	}
	
}




?>