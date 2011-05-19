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

	static protected $loaded_controller, $loaded_model;
	static protected $models_dir = MODELS_DIR, $library_dir = LIBRARY_DIR;
        static protected $controllers_dir = CONTROLLERS_DIR;
        static protected $controller_loaded = array();

	/**
	 * load a controller and return the html
	 *
	 */
	function load_controller( $controller, $object_name = null, $controller_extension = CONTROLLER_EXTENSION, $controller_class_name = CONTROLLER_CLASS_NAME ){

		if(!$object_name)
			$object_name = $controller;

                // transform the controller string to capitalized. e.g. user => User, news_list => News_List
                $controller = implode( "_", array_map( "ucfirst", array_map( "strtolower", explode( "_", $controller ) ) ) );

		// include the file
		if( file_exists( $controller_file = self::$controllers_dir . "$controller/$controller." . $controller_extension ) )
			require_once $controller_file;
		else{
                     trigger_error( "CONTROLLER: FILE <b>{$controller_file}</b> NOT FOUND ", E_USER_WARNING );
                     return false;
                }

		$class = $controller . $controller_class_name;

		if( class_exists($class) )
			$this->$object_name = new $class( $this );
		else
			return trigger_error( "CONTROLLER: CLASS <b>{$controller}</b> NOT FOUND ", E_USER_WARNING );

                self::$controller_loaded[] = array( 'controller'=>$controller );

                return true;
	}



	/**
	 * Load the model class
	 *
	 * @param string $model Model to load
	 * @param string $object_name Name to access the model
	 * @return boolean true if the model was loaded
	 */
	function load_model($model,$object_name=null){

                // load the model class
                require_once LIBRARY_DIR . "Model.php";

		if(!$object_name)
			$object_name = $model;

                // transform the model string to capitalized. e.g. user => User, news_list => News_List
                $model = implode( "_", array_map( "ucfirst", array_map( "strtolower", explode( "_", $model ) ) ) );


		// include the file
		if( file_exists($file = self::$models_dir . $model . ".php") )
			require_once $file;
		else{
			trigger_error( "MODEL: FILE <b>{$file}</b> NOT FOUND ", E_USER_WARNING );
			return false;
		}

		$class = $model . "_Model";
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


		if( !$object_name )
                    $object_name = $library;

                // transform the library string to capitalized. e.g. user => User, news_list => News_List
                $library = implode( "_", array_map( "ucfirst", array_map( "strtolower", explode( "_", $library ) ) ) );


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
        function filter_before(){}



        /**
         * Called before init the controller
         */
        function filter_after(){}


}



// -- end