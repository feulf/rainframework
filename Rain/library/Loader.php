<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


require_once LIBRARY_DIR . "functions.php";

    // get the execution time and total memory
    timer_start();
    memory_usage_start();

require_once CONSTANTS_DIR  . "constants.php";
require_once LIBRARY_DIR . "error.functions.php";
require_once LIBRARY_DIR . "Controller.php";

/**
 * Load and init all the classes of the framework
 */
class Loader{


        protected static $instance;   // class instance for singleton calls

        // controller settings
        protected static $controller_extension = CONTROLLER_EXTENSION, $controller_class_name = CONTROLLER_CLASS_NAME;

        protected $page_layout = "index",             // default page layout
                  $not_found_layout = "not_found";    // default page layout not found

        // ajax variables
        protected $ajax_mode = false,
                  $load_javascript = false,
                  $load_style = false;

        protected $var,                   // variables assigned to the page layout
                  $load_area_array = array();   // variables assigned to the page layout

        // selected controller
        protected $selected_controller = null,
                  $selected_action = null,
                  $selected_params = null,
                  $loaded_controller = array();


        /**
         * Return the object Loader
         * @return Loader
         */
        static function get_instance(){
            if( !self::$instance )
                self::$instance = new self;

            return self::$instance;
        }



        /**
         * Load the content selected by the URI and save the output in load_area.
         * Leave the parameters null if you want to load automatically the controllers
         */
        function load_controller( $controller = null, $action = null, $params = array(), $load_area = "center" ){

            // if is not selected the controller, get it automatically from the URI
            if( !$controller ){
                // load the Router library and get the URI
                require_once LIBRARY_DIR . "Router.php";
                $router = new Router;
                $this->selected_controller_dir  = $controller_dir   = $router->get_controller_dir();
                $this->selected_controller      = $controller       = $router->get_controller();
                $this->selected_action          = $action           = $router->get_action();
                $this->selected_params          = $params           = $router->get_params();
                unset( $router );
            }

            // init the controller class
            $controller_obj = new Controller;

            // check if the controller can be loaded, and if the action can be executed
            if( $controller_obj->load_controller( $controller, "controller_obj", self::$controller_extension, self::$controller_class_name ) && is_callable( array($controller_obj->controller_obj,$action) ) ){

                timer_start("controller");
                memory_usage_start("controller");

                // get all the output from the controller
                ob_start();

                // call the method filter_before
                call_user_func_array( array( $controller_obj->controller_obj, "filter_before" ), $params );

                // call the selected action
                call_user_func_array( array( $controller_obj->controller_obj, $action ), $params );
                
                //call the method filter_after
                call_user_func_array( array( $controller_obj->controller_obj, "filter_after" ), $params );

                $html = ob_get_contents();
                ob_end_clean();

                $this->loaded_controller[] = array( "controller" => $controller, "execution_time" => timer("controller"), "memory_used" => memory_usage("controller") );

                // if it is in ajax mode print and stop the execution of the script
                if( $this->ajax_mode ){
                    echo $html;
                    $this->_draw_ajax();
                }
                else{
                    // save the output into the load_area array
                    if( !isset($this->load_area_array[$load_area]) )
                        $this->load_area_array[$load_area] = array();

                    $this->load_area_array[$load_area][] = $html;
                }


            }
            elseif( $this->ajax_mode )
                    die;
            else
                    $this->_draw_page_not_found("controller_not_found");

        }



        /**
         * Load the model
         */
        function load_model( $model, $action, $params, $assign_to = null ){

                $controller_obj = new Controller;
                if( $controller_obj->load_model( $model, "model_obj" ) ){
                        if( is_callable( array($controller_obj->model_obj, $action) ))
                            $return = call_user_func_array( array( $controller_obj->model_obj, $action ), $params );
                        else{
                            // model not found
                        }
                        $this->assign( $assign_to, $return );
                }

        }



        /**
         *
         * @param <type> $helper
         */
        function load_helper( $helper ){
            if( is_array( $helper ) )
                array_map( array($this,"load_helper"), $helper );
            else
                require_once HELPER_DIR . $helper . ".php";
        }



        /**
         * Load the settings file
         */
        function init_settings( $config_dir = CONFIG_DIR, $settings_file = "settings.php" ){
                require_once $config_dir . $settings_file;
        }



        /**
         * Init the database class
         */
        function init_db(){
            require_once LIBRARY_DIR . "DB.php";
        }




        /**
         * Init the session class
         */
        function init_session(){
            require_once LIBRARY_DIR . "Session.php";
            session::get_instance();
        }




        /**
         * Init the user
         */
        function init_user(){
            require_once LIBRARY_DIR . "User.php";
            new User;
        }



        /**
         * User login
         */
        function auth_user(){
            $this->init_user();
            User::Login( post('login'), post('password'), post('cookie'), get_post('logout') );
        }




        /**
         * Init the language
         */
        function init_language( $lang_id = "en" ){
		if( file_exists( LANGUAGE_DIR . $lang_id . '/generic.php') ){
			require_once LANGUAGE_DIR . $lang_id . '/generic.php';
			define( "LANG_ID", $lang_id );
		}
                else
                    $this->page_not_found = true;
        }




        /**
         * Init the theme
         */
        function init_theme( $theme = null ){

                // Init the view class
                require_once LIBRARY_DIR . "View.php";

                // if theme dir is a directory
		if( is_dir( $theme_dir = VIEWS_DIR . $theme . ( ( !$theme or substr($theme,-1,1) ) =="/" ? null : "/" ) ) ){
			define( "THEME_DIR", $theme_dir );
                        View::configure( "tpl_dir", THEME_DIR );
                        View::configure( "cache_dir", CACHE_DIR );
                        View::configure( "base_url", URL );
		}
		else
			$this->_draw_page_not_found("theme_not_found");

        }



        /**
         * Init eventual Javascript useful for the application.
         * Extends the class Loader if you have to load more javascript
         */
        function init_js(){
                add_javascript( "var url = '" . URL . "';" );
        }



        /**
         * Init the page layout
         */
        function init_page_layout( $page_layout ){
                $this->page_layout = $page_layout;
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
	 */
	function draw( $return_string = false ){

		$tpl = new View;

                // assign all variable
		$tpl->assign( $this->var );
                
                
                // - LOAD AREA ----
                // wrap all the blocks in a load area
                foreach( $this->load_area_array as $load_area_name => $blocks_array )
                    $load_area[$load_area_name] = $this->_blocks_wrapper($blocks_array);
                $tpl->assign( "load_area", $load_area );
                // ----------------

		// - HEAD ------
		$head = get_javascript() . get_style();
		$tpl->assign( "head", $head );
		// --------------

		// - BENCHMARK ------
		$tpl->assign( "execution_time", timer() );
                $tpl->assign( "memory_used", memory_usage() );
                $tpl->assign( "loaded_controller", $this->loaded_controller );
		$tpl->assign( "n_query", class_exists('db') ? db::get_executed_query() : null );
		// --------------

		return $tpl->draw( $this->page_layout, $return_string );

	}




	/**
	 * This function can be called by the Controller.
	 * It disable the loading of layout and optionally can enables/disables the loading of javascript and style
	 */
	function ajax_mode( $load_javascript = false, $load_style = false, $ajax_mode = true ){
		$this->ajax_mode = true;
		$this->load_javascript = $load_javascript;
		$this->load_style = $load_style;
	}



        /**
         * Get the selected controller dir
         * @return string
         */
        function get_selected_controller_dir(){
                return $this->selected_controller_dir;
        }



        /**
         * Get the selected controller
         * @return string
         */
        function get_selected_controller(){
                return $this->selected_controller;
        }



        /**
         * Get the selected controller
         * @return string
         */
        function get_selected_action(){
                return $this->selected_action;
        }



        /**
         * Get the selected controller
         * @return string
         */
        function get_selected_params(){
                return $this->selected_params;
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
         * Page was not found
         */
        protected function _draw_page_not_found( $msg = "page_not_found" ){
                header("HTTP/1.0 404 Not Found");
                $this->page_layout = $this->not_found_layout;
                $this->assign( "message", get_msg($msg) );
                $this->draw();
                die;
        }


	/**
	 * ajax call
	 */
	protected function _draw_ajax(){
		$html =  $this->load_style ? get_style() : null;
                $html .= $this->load_javascript ? get_javascript() : null;
		die( $html );
	}
        
        
        
        protected function _blocks_wrapper( $block_array = array() ){
            $html = null;
            foreach( $block_array as $block_html )
                $html .= $block_html;
            return $html;
        }


        protected function  __construct() {}


}