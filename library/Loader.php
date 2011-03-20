<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


require_once LIBRARY_DIR . "functions.php";         // functions
require_once LIBRARY_DIR . "error.functions.php";   // error manager

require_once LIBRARY_DIR . "Model.php";             // model
require_once LIBRARY_DIR . "View.php";              // view
require_once LIBRARY_DIR . "Controller.php";        // controller



/**
 * It loads all necessary of your application
 *
 */
class Loader{

	private $var, $page, $page_not_found;
	private $controller, $action, $params, $controller_dir;
	private $controllers_dir = CONTROLLERS_DIR, $models_dir = MODELS_DIR, $views_dir = VIEWS_DIR;
	private $controller_extension, $controller_class_name, $controller_dir_in_route;
	private $ajax_mode = false, $load_javascript = false, $load_style = false;

	/**
	 * Constructor
	 * 
	 */
	function __construct(){

		// Timer Start to get the execution time
		timer_start();

	}



	/**
	 * Connect to the database
	 *
	 */
	function database_connect(){
                require_once LIBRARY_DIR . "DB.php";
		$this->db = new DB;
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
			date_default_timezone_set( get_setting('timezone') );
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
	 * Init the user functions
	 *
	 */
	function init_user(){
                require_once LIBRARY_DIR . "User.php";    // user
                new User;
	}



	/**
	 * User authentication
	 *
	 */
	function login(){
		if( $this->db ){
                        load_lang('user');
                        $this->init_user();
			$login_status = User::login( get( 'login' ), get( 'password' ), get( 'cookies' ), get( 'logout' ) );
		}

	}



	/**
	 * Set the View class
	 *
	 */
	function set_theme( $theme = null ){
		if( $theme )
			$theme .= "/";

		if( is_dir( $this->views_dir . $theme ) ){
			define( "THEME_DIR", $this->views_dir . $theme . "/" );
                        View::configure( "tpl_dir", THEME_DIR );
                        View::configure( "cache_dir", CACHE_DIR );
                        View::configure( "base_url", URL );
		}
		else
			trigger_error( "THEME NOT FOUND: $theme", E_USER_WARNING );

	}


	/**
	 * Set Page Layout
	 *
	 */
	function set_page( $page ){
		$this->page = $page;
	}
	


	/**
	 * Set the controller extension
	 *
	 */
	function set_controller_extension( $controller_extension ){
		$this->controller_extension = $controller_extension;
	}
	
	

	/**
	 * Set the controller class name
	 *
	 */
	function set_controller_class_name( $controller_class_name ){
		$this->controller_class_name = $controller_class_name;
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
		if( $this->controller_dir_in_route )
			$this->controller_dir	= is_array($route_array) && count($route_array) ? array_shift($route_array) : get_setting("default_controller_dir");
		$this->controller	= is_array($route_array) && count($route_array) ? array_shift($route_array) : get_setting("default_controller");
		$this->action 		= is_array($route_array) && count($route_array) ? array_shift($route_array) : get_setting("default_action");
		$this->params 		= $route_array;

	}
	
	
	
	/**
	 * load the controller selected by the route
	 */
	function auto_load_controller(){
		$this->load_controller( $this->controller, $this->action, $this->params, $this->controller_dir, $assign_to = "center" );
	}



	/**
	 * load a controller and return the html
	 *
	 */
	function load_controller( $controller, $action = null, $params = null, $controller_dir = null, $assign_to = null ){

		if( !$controller_dir ) $controller_dir = $controller;
		$controller_extension = $this->controller_extension ? $this->controller_extension : CONTROLLER_EXTENSION;
		$controller_class_name = $this->controller_class_name ? $this->controller_class_name : CONTROLLER_CLASS_NAME;

		// include the file
		if( file_exists( $controller_file = $this->controllers_dir . "$controller_dir/$controller." . $controller_extension ) )
			require_once $controller_file;
		else{
			header("HTTP/1.0 404 Not Found");
			trigger_error( "CONTROLLER: FILE <b>{$controller_file}</b> NOT FOUND ", E_USER_WARNING );
			$this->page_not_found = true;
			return false;
		}

		$html = "";
		$class = $controller . $controller_class_name;
		if( class_exists($class) )
			$controller_obj = new $class( $this );
		else{
			header("HTTP/1.0 404 Not Found");
			trigger_error( "CONTROLLER: CLASS <b>{$controller}</b> NOT FOUND ", E_USER_WARNING );
			$this->page_not_found = true;
			return false;
		}

		if( $action ){
			if( is_callable( array($controller_obj,$action) )){
				ob_start();
				for($i=0,$n=count($params),$param="";$i<$n;$i++)
					$param .= $i>0 ? ',$params['.$i.']' : '$params['.$i.']';
				eval( '$controller_obj->$action( ' . $param . ' );' );
				$html = ob_get_contents();
				ob_end_clean();
			}
			else{
				header("HTTP/1.0 404 Not Found");
				$this->page_not_found = true;
			}
		}
		
		if( $assign_to )
			$this->assign( $assign_to, $html );

		return $html;

	}



	/**
	 * load model and return the result
	 *
	 */
	function load_model( $model, $action = null, $params = null, $assign_to = null ){
		$con = new Controller($this);
		$con->set_models_dir = $this->models_dir;
		if( $con->load_model( $model, "model_obj" ) ){
			
			if( is_callable( array($con->model_obj, $action) )){

				for($i=0,$n=count($params),$param="";$i<$n;$i++)
					$param .= $i>0 ? ',$params['.$i.']' : '$params['.$i.']';
				eval( '$return = $con->model_obj->$action( ' . $param . ' );' );
			}
			else{
				// model not found
			} 
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
		else
			$this->var[ $variable ] = $value;
	}



	/**
	 * Draw the output
	 *
	 */
	function draw( $return_string = false ){

		if( $this->ajax_mode )
			return $this->_ajax_call( $return_string );

		$tpl = new View();
		$tpl->assign( $this->var );// assign all variable
		
		// - HEAD ------
		$head = $this->_get_javascript() . $this->_get_style();
		$tpl->assign( "head", $head );
		// --------------

		// - DEBUG ------
		$tpl->assign( "execution_time", timer() );
		$tpl->assign( "n_query", $this->db ? $this->db->get_executed_query() : 0 );
		// --------------

		if( $this->page_not_found )
			$this->page = get_setting('page_not_found');

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
	 * This function can be called by the Controller.
	 * It disable the loading of layout and optionally can enables/disables the loading of javascript and style
	 * 
	 */
	function ajax_mode( $load_javascript = false, $load_style = false ){
		$this->ajax_mode = true;
		$this->load_javascript = $load_javascript;
		$this->load_style = $load_style;
	}
	
	
	
	/**
	 * ajax call
	 */
	function _ajax_call( $return_string = true ){
		$html = "";
		if( $this->load_javascript )
			$html .= $this->_get_javascript();
		if( $this->load_style )
			$html .= $this->_get_style();
		$html .= $this->var['center'];

		// print the controller auto loaded
		if( $return_string ) return $html; else echo $html; 
	}


	/**
	 * get javascript
	 */
	private function _get_javascript(){
		global $script, $javascript, $javascript_onload;
		$html = "";
		if( $script )
			foreach( $script as $s )
				$html .= '	<script src="'.$s.'" type="text/javascript"></script>' . "\n";
		if( $javascript_onload ) $javascript .=  "\n" . "$(function(){" . "\n" . "	$javascript_onload" . "\n" . "});" . "\n";
		if( $javascript )
			$html .= "<script type=\"text/javascript\">" . "\n" .$javascript . "\n" . "</script>";
		return $html;
	}
	
	
	
	/**
	 * get the style
	 */
	private function _get_style(){
		global $style;
		$html = "";
		if( $style )
			foreach( $style as $s )
				$html .= '	<link rel="stylesheet" href="'.$s.'" type="text/css" />' . "\n";
		return $html;
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