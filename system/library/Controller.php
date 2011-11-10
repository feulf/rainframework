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

	static protected $models_dir = MODELS_DIR, $library_dir = LIBRARY_DIR;
	static protected $controllers_dir = CONTROLLERS_DIR;
	static protected $controller_loaded = array();

	/**
	 * load a controller and return the html
	 *
	 */
	function load_controller( $controller, $action = null, $params = null, $load_area = null ){

		// get the loader
		$loader = Loader::get_instance();
		$loader->load_controller( $controller, $action, $params, $load_area );

	}



	/**
	 * Load the model class
	 *
	 * @param string $model Model to load
	 * @param string $object_name Name to access the model
	 * @return boolean true if the model was loaded
	 */
	function load_model($model,$object_name=null){

		if( !$object_name )
			$object_name = $model;

		// get the loader
		$loader = Loader::get_instance();
		
		// assign the model to the object name, so now it's accessible from the controller
		$this->$object_name = $loader->load_model( $model );

	}


	/**
	 * Load the library
	 *
	 */
	function load_library( $library, $object_name = null ){


		if( !$object_name )
			$object_name = $library;

		// transform the library string to capitalized. e.g. user => User, news_list => News_List
		$library = implode( "_", array_map( "strtolower", explode( "_", $library ) ) );


		if( file_exists($file = self::$library_dir . $library . ".php") )
			require_once $file;
		else{
			trigger_error( "LIBRARY: FILE <b>{$file}</b> NOT FOUND ", E_USER_WARNING );
			return false;
		}


		$class = $library;
		if( class_exists($class) )
			$this->$object_name = new $class;
		else{
			trigger_error( "LIBRARY: CLASS <b>{$library}</b> NOT FOUND", E_USER_WARNING );
			return false;
		}
		return true;

	}

	/**
	 * Enable the Ajax mode.
	 * If you call this function the Loader class, will print the output of the controller
	 * without loading any Layout.
	 *
	 */
	function ajax_mode( $load_javascript = false, $load_style = false, $load_layout = false){
		$loader = Loader::get_instance();
		$loader->ajax_mode( $load_javascript, $load_style, $load_layout );
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
        
        

	/**
	 * Called before init the controller
	 */
	public function filter_before(){}



	/**
	 * Called before init the controller
	 */
	public function filter_after(){}


}



// -- end