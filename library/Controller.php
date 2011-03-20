<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



/**
 * Controller class
 */
class Controller{

	static $loaded_controller, $loaded_model;
	private $models_dir = MODELS_DIR, $library_dir = LIBRARY_DIR, $loader;
	
	function __construct( $loader ){
		$this->loader = $loader;
	}

	
	/**
	 * Load the model class
	 *
	 * @param string $model Model to load
	 * @param string $object_name Name to access the model
	 * @return boolean true if the model was loaded
	 */
	function load_model($model,$object_name=null){

                $model = ucfirst(strtolower($model)); // first char of model must be uppercase

		// include the file
		if( file_exists($file = $this->models_dir . $model . ".php") )
			require_once $file;
		else{
			trigger_error( "MODEL: FILE <b>{$file}</b> NOT FOUND ", E_USER_WARNING );
			return false;
		}

		if(!$object_name)
			$object_name = $model;

		$class=$model . "_Model";
		if( class_exists($class) )
			$this->$object_name = new $class;			
		else{
			trigger_error( "MODEL: CLASS <b>{$model}</b> NOT FOUND", E_USER_WARNING );
			return false;
		}
		return true;
	}
	
	
	/**
	 * Load the library
	 *
	 */
	function load_library( $library, $object_name = null ){

                $library = ucfirst(strtolower($library));

		if( file_exists($file = $this->library_dir . $library . ".php") )
			require_once $file;
		else{
			trigger_error( "LIBRARY: FILE <b>{$file}</b> NOT FOUND ", E_USER_WARNING );
			return false;
		}
		
		if(!$object_name)
			$object_name = $library;

		$class = $library;
		if( class_exists($class) )
			$this->$object_name = new $class;			
		else{
			trigger_error( "LIBRARY: CLASS <b>{$library}</b> NOT FOUND", E_USER_WARNING );
			return false;
		}
		return true;

	}
	
	function set_library_dir( $library_dir ){
		$this->library_dir = $library_dir;
	}	
	
	function set_controllers_dir( $controllers_dir ){
		$this->controllers_dir = $controllers_dir;
	}

	function set_models_dir( $models_dir ){
		$this->models_dir = $models_dir;
	}

	/**
	 * Enable the Ajax mode.
	 * If you call this function the Loader class, will print the output of the controller
	 * without loading any Layout.
	 *
	 */
	function ajax_mode( $load_javascript = false, $load_style = false, $load_layout = false){
		$this->loader->ajax_mode( $load_javascript, $load_style, $load_layout );
	}

	
}



?>