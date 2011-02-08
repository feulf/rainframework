<?php

/**
 *	Rain Framework > Loader class
 *	-----------------------------
 * 
 *	Load all classes and functions and init all directive
 * 
 *	@author Federico Ulfo
 *	@copyright developed and mantained by the Rain Team: http://www.raintm.com
 *	@license Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *	@link http://www.rainframework.com
 *	@package RainFramework
 */


require_once LIBRARY_DIR . "functions.php";			// functions	
require_once LIBRARY_DIR . "error.php";				// error manager
require_once LIBRARY_DIR . "db.class.php";			// database
require_once LIBRARY_DIR . "rain.tpl.class.php";	// template
require_once LIBRARY_DIR . "controller.class.php";	// controller
require_once LIBRARY_DIR . "model.class.php";		// model
require_once LIBRARY_DIR . "user.functions.php";	// user



/**
 * It loads all necessary of your application
 *
 */
class Loader{

	private $var, $page;
	private $controller, $action, $params;
	public $controllers_dir = CONTROLLERS_DIR, $models_dir = MODELS_DIR;

	/**
	 * Constructor
	 * 
	 */
	function Loader(){

		// Timer Start to get the execution time
		timer_start();

	}



	/**
	 * Connect to the database
	 *
	 */
	function database_connect(){
		$this->db = new DB();
		$this->db->connect();
	}




	/**
	 * Load all settings
	 *
	 */
	function load_settings(){
		require_once CONFIG_DIR . "settings.php";
		
		// Set the timezone
		if( function_exists( "date_default_timezone_set" ) )
			date_default_timezone_set( TIMEZONE );
	}



	/**
	 * Set the language
	 *
	 */
	function set_language( $lang_id = null ){
		if( file_exists(LANGUAGE_DIR . $lang_id . '/generic.php') ){
			require_once LANGUAGE_DIR . $lang_id . '/generic.php';
			define( "LANG_ID", $lang_id );
		}
	}


	
	/**
	 * User authentication
	 *
	 */
	function login(){
		if( $this->db )
			$login_status = login( get( 'login' ), get( 'password' ), get( 'cookies' ), get( 'logout' ) );
	}



	/**
	 * Connect to the database
	 *
	 */
	function set_theme( $theme ){
		if( is_dir( TEMPLATES_DIR . $theme ) ){
			define( "THEME_DIR", TEMPLATES_DIR . $theme . "/" );
			raintpl::$tpl_dir = THEME_DIR;
			raintpl::$cache_dir = CACHE_DIR;
			raintpl::$base_url = URL;
		}
	}


	/**
	 * Set Page Layout
	 *
	 */
	function set_page( $page ){
		$this->page = $page;
	}
	
	
	/**
	 * Set the route from the URI (ex. index.php/blog/list/)
	 *
	 */
	function init_route(){

		$directory = dirname($_SERVER['SCRIPT_NAME']) . "/";
		$script = basename($_SERVER['SCRIPT_NAME']);
		$route = substr( $_SERVER['REQUEST_URI'], strlen($directory) );
		if(substr($route,0,strlen($script))==$script)
			$route = substr($route,strlen($script)+1);

		preg_match_all( "#((?:(\w*?)/))#", $route, $match );
		$route_array=$match[2];
		$this->controller	= is_array($route_array) && count($route_array) ? array_shift($route_array) : DEFAULT_CONTROLLER;
		$this->action 		= is_array($route_array) && count($route_array) ? array_shift($route_array) : DEFAULT_ACTION;
		$this->params 		= $route_array;

	}
	
	
	
	/**
	 * load the controller selected by the route
	 */
	function auto_load_controller(){
		$html = $this->load_controller( $this->controller, $this->action, $this->params );
		$this->assign("center",$html);
	}



	/**
	 * load a controller and return the html
	 *
	 */
	function load_controller( $controller, $action = null, $params = null ){
		$con = new Controller;
		$con->set_controllers_dir($this->controllers_dir);
		if( $con->load_controller( $controller, "controller_obj" ) ){
			ob_start();
				if( $action )
					if( is_callable( array($con->controller_obj,$action) )){

						for($i=0,$n=count($params),$param="";$i<$n;$i++)
							$param .= $i>0 ? ',$params['.$i.']' : '$params['.$i.']';
						eval( '$return = $con->controller_obj->$action( ' . $param . ' );' );
					}
					else
						$this->page = PAGE_NOT_FOUND;				
				$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}else
			$this->page = PAGE_NOT_FOUND;				

	}
	
	
	
	/**
	 * load model and return the result
	 *
	 */
	function load_model( $model, $action = null, $params = null, $assign_to = null ){
		$con = new Controller;
		$con->set_models_dir = $this->models_dir;
		if( $con->load_model( $model, "model_obj" ) ){
			if( $action )
				$return = $con->model_obj->$action( $params );
			$this->assign( $assign_to, $return );
		}
	}
	


	/**
	 * Assign value to the page layout
	 *
	 * @param mixed $variable
	 * @param string $value
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
	 * Draw the output
	 *
	 */
	function draw( $return_string = false ){
		$tpl = new RainTPL();
		$tpl->assign( $this->var );// assign all variable
		
		// - DEBUG ------
		$tpl->assign( "execution_time", timer() );
		$tpl->assign( "n_query", $this->db ? $this->db->get_executed_query() : 0 );
		// --------------
		
		return $tpl->draw( $this->page, $return_string );
	}
	

	
	
	/**
	 * Return the selected controller
	 *
	 */
	function get_selected_controller(){
		return $this->controller;
	}
	
	

	/**
	 * Return the selected controller
	 *
	 */
	function get_selected_action(){
		return $this->action;
	}



	/**
	 * Return the selected controller
	 *
	 */
	function get_selected_params(){
		return $this->params;
	}



	/**
	 * Destroy the class
	 *
	 */
	function __destruct(){
		if( $this->db )
			$this->db->disconnect();
	}



}


?>