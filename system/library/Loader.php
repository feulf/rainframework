<?php

/**
 *  RainFramework
 *  -------------
 *  Realized by Federico Ulfo & maintained by the Rain Team
 *  Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
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
        protected static $controllers_dir = CONTROLLERS_DIR, 
                         $controller_extension = CONTROLLER_EXTENSION,
                         $controller_class_name = CONTROLLER_CLASS_NAME,
                         $models_dir = MODELS_DIR;

        protected $page_layout = "index",             // default page layout
                  $not_found_layout = "not_found";    // default page layout not found

        // ajax variables
        protected $ajax_mode = false,
                  $load_javascript = false,
                  $load_style = false;

        protected $var = array(),                   // variables assigned to the page layout
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
        
        
        
        function auto_load_controller(){
                // load the Router library and get the URI
                require_once LIBRARY_DIR . "Router.php";
                $router = new Router;
                $this->selected_controller_dir  = $controller_dir   = $router->get_controller_dir();
                $this->selected_controller      = $controller       = $router->get_controller();
                $this->selected_action          = $action           = $router->get_action();
                $this->selected_params          = $params           = $router->get_params();
                
                $this->load_controller($controller, $action, $params );
        }



        /**
         * Load the content selected by the URI and save the output in load_area.
         * Leave the parameters null if you want to load automatically the controllers
         * 
         * @param string $controller selected controller
         * @param string $action selected action
         * @param string $params array of the selected actions
         * @param string $load_area selected load area where the controller is rendered
         */
        function load_controller( $controller = null, $action = null, $params = array(), $load_area = "center" ){


            // transform the controller string to capitalized. e.g. user => user, news_list => news_list
            $controller = strtolower( $controller );
            $controller_file = self::$controllers_dir . "$controller/$controller." . self::$controller_extension;

            // include the file
            if( file_exists( $controller_file = self::$controllers_dir . "$controller/$controller." . self::$controller_extension ) )
				require_once $controller_file;
            else
				return trigger_error( "CONTROLLER: FILE <b>{$controller_file}</b> NOT FOUND ", E_USER_WARNING );

                    
            // define the class name of the controller
            $class = $controller . self::$controller_class_name;



            // check if the controller class exists
            if( class_exists($class) )
				$controller_obj = new $class( $this );
            else
				return trigger_error( "CONTROLLER: CLASS <b>{$controller}</b> NOT FOUND ", E_USER_WARNING );
                    

            if( $action ){

                // start benchmark
                timer_start("controller");
                memory_usage_start("controller");

                // start the output buffer
                ob_start();

                // call the method filter_before
                call_user_func_array( array( $controller_obj, "filter_before" ), $params );

                // call the selected action
                $action_status = call_user_func_array( array( $controller_obj, $action ), $params );

                //call the method filter_after
                call_user_func_array( array( $controller_obj, "filter_after" ), $params );

                $html = ob_get_contents();

                // close the output buffer
                ob_end_clean();


                // verify that the action was executed
                if( false === $action_status )
                    $html = "Action <b>$action</b> not found in controller <b>$class</b>! Method not declared or declared with different private access";


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
                    

        }


        /**
         * Load the model
         * 
         * @param string $model selected model
         * @param string $action selected action
         * @param array $params parameters
         * @param string $assign_to variable where you assign the result of the model
         */
        function load_model( $model ){

			// load the model class
			require_once LIBRARY_DIR . "Model.php";
                
			// transform the model string to capitalized. e.g. user => User, news_list => News_List
			$model = implode( "_", array_map( "ucfirst",  explode( "_", $model )  ) );
        	
			// include the file
			if( file_exists($file = self::$models_dir . $model . ".php") )
				require_once $file;
			else{
				trigger_error( "MODEL: FILE <b>{$file}</b> NOT FOUND ", E_USER_WARNING );
				return false;
			}

			// class name
			$class = $model . "_Model";

			// test if the class exists
			if( class_exists($class) )
				return new $class;
			else{
				trigger_error( "MODEL: CLASS <b>{$model}</b> NOT FOUND", E_USER_WARNING );
				return false;
			}

        }
        
        
        
        function load_menu(){
            $menu_obj = $this->load_model( "menu" );
            $menu_list = $menu_obj->load_menu();
            $this->assign( "menu", $menu_list );
        }



        /**
         *
         * @param <type> $helper
         */
        function load_helper( $helper ){
            if( is_array( $helper ) )
                array_map( array($this,"load_helper"), $helper );
            else
                require_once LIBRARY_DIR . $helper . ".php";
        }



        /**
         * Load the settings file
         */
        function init_settings( $config_dir = CONFIG_DIR, $settings_file = "settings.php" ){
                require_once $config_dir . $settings_file;
                require_once CONFIG_DIR . "url.php";
        }



        /**
         * Init the database class
         */
        function init_db($name = null){
            require_once LIBRARY_DIR . "DB.php";
			
			if(!$name)
				$name = DB::DEFAULT_CONNECTION_NAME;
				
			db::init($name);
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




        function init_language() {

            $installed_language = get_installed_language();
            $installed_language = array_flip( $installed_language );
            
            // get the language
            if (get('set_lang_id'))
                $lang_id = get('set_lang_id');
            elseif (isset($_SESSION['lang_id']))
                $lang_id = $_SESSION['lang_id'];
            else
                $lang_id = get_setting('lang_id');

            // language not found, load the default language
            if (!isset($installed_language[$lang_id])) {
                $default_language = array_pop($installed_language);
                $lang_id = $default_language['lang_id'];
            }

            // set the language in session
            $_SESSION['lang_id'] = $lang_id;

            // define the constant
            define("LANG_ID", $lang_id);

            // load the dictionaries
            load_lang('generic');
            
        }




        /**
         * Init the theme
         * 
         * @param string $theme selected theme
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
         * 
         * @param string $page_layout selected page layout
         */
        function init_page_layout( $page_layout ){
                $this->page_layout = $page_layout;

                // init the load area array
                $this->_get_load_area();
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
         * @param bool $return_string if true return a string else draw the page
         * @return string
	 */
	function draw( $return_string = false ){

		$tpl = new View;

                // assign all variable
		$tpl->assign( $this->var );
                
                
                // - LOAD AREA ----
                // wrap all the blocks in a load area
                if( $this->load_area_array ){
                    foreach( $this->load_area_array as $load_area_name => $blocks_array )
                        $load_area[$load_area_name] = $this->_blocks_wrapper($blocks_array);
                    $tpl->assign( "load_area", $load_area );
                }
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
         * 
         * @param bool $load_javascript if true Rain load the javascript
         * @param bool $load_style if true Rain load the stylesheet
         * @param bool $ajax_mode if true it set the ajax mode
	 */
	function ajax_mode( $load_javascript = false, $load_style = false, $ajax_mode = true ){
		$this->ajax_mode = $ajax_mode;
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
	 * Configure the settings,
         * settings are static variable to setup this class
         * 
         * @param string $setting setting name
         * @param string $value value of the setting
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
         * 
         * @param string $msg, message for the page not found
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
        
        
        /**
         * wrap all blocks of an load area
         */
        protected function _blocks_wrapper( $block_array = array() ){
            $html = null;
            if( $block_array )
                foreach( $block_array as $block_html )
                    $html .= $block_html;
            return $html;
        }
        
        /**
         *  init the load_area.php file that define all the load area of the template page
         */
        protected function _get_load_area( ){

                if( !file_exists( $src = CACHE_DIR . "load_area." . md5( THEME_DIR . $this->page_layout ) . ".php" ) || ( filemtime($src) != filemtime( THEME_DIR . $this->page_layout . ".html" ) ) ){

                        $dir = explode( "/", CACHE_DIR . THEME_DIR );
                        for( $i=0, $base=""; $i<count($dir);$i++ ){
                                $base .= $dir[$i] . "/";
                                if( !is_dir($base) )
                                        mkdir( $base );
                        }

                        preg_match_all( '/\{\$load_area\.(.*?)\}/si', $file = file_get_contents( THEME_DIR . $this->page_layout . '.html' ), $match );
                        $php = "<?php\n\$load_area=array(";
                        for( $i = 0; $i < count( $match[1] ); $i++ )
                                $php .= "'{$match[1][$i]}'=>'',";
                        $php .= ");\n?>";

                        file_put_contents( $src, $php );
                }

                require $src;
                $this->load_area_array = $load_area;
        }	



        protected function  __construct() {}


}
