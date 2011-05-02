<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


/**
 * Set the route getting the user input
 */
class Router{

        private static  $config_dir = CONFIG_DIR,   // config directory
                        $config_route,              // config route
                        $route,                     // defined route
                        $controller_dir,            // controller dir
                        $controller,                // controller
                        $action,                    // action
                        $params = array();          // parameters



        function __construct(){
                require_once self::$config_dir . "route.php";
                if( !self::$config_route )
                    self::$config_route = $route;

                if( !self::$route )
                    return $this->_init();
        }



        /**
         * return the selected route
         * @return string
         */
        function get_route(){
                return self::$route;
        }

        /**
         * return the selected controller dir
         * @return string
         */
        function get_controller_dir(){
                return self::$controller_dir;
        }

        /**
         * return the selected controller
         * @return string
         */
        function get_controller(){
                return self::$controller;
        }

        /**
         * return the selected action
         * @return string
         */
        function get_action(){
                return self::$action;
        }

        /**
         * return the selected parameters
         * @return string
         */
        function get_params(){
                return self::$params;
        }

        private function _init(){

                $route = $this->_set_route();
                $route = substr($route,-1,1) == "/" ? substr($route,0,-1) : $route;
                $route = $route ? explode( "/", $route ) : null;

                // define the controller directory
                if( isset( self::$config_route['controller_dir_in_route'] ) && self::$config_route['controller_dir_in_route'] === true ){
                        if( is_array($route) && count($route) )
                                self::$controller_dir = array_shift($route);
                        elseif( isset( self::$config_route["default_controller_dir"] ) )
                                self::$config_route["default_controller_dir"];
                        else
                                trigger_error( "ROUTER: DEFAULT CONTROLLER DIR NOT SET");
                }

                // define the controller
                if( is_array($route) && count($route) )
                        self::$controller = array_shift($route);
                elseif( isset( self::$config_route['default_controller'] ) )
                        self::$controller = self::$config_route["default_controller"];
                else
                        trigger_error( "ROUTER: DEFAULT CONTROLLER NOT SET");

                // define action
                if( is_array($route) && count($route) )
                        self::$action = array_shift($route);
                elseif( isset( self::$config_route['default_action'] ) )
                        self::$action = self::$config_route['default_action'];
                else
                        trigger_error( "ROUTER: DEFAULT ACTION NOT SET" );

                // define the parameters
                if( $route )
                        self::$params = $route;
        }

        private function _set_route(){

            $route = isset( $_SERVER["PATH_INFO"] ) ? substr($_SERVER["PATH_INFO"],1) : null;
            $config_route = self::$config_route;

            foreach( $config_route as $key => $value ){
                    $key = str_replace( ':any', '.+', $key );
                    $key = str_replace( ':num', '[0-9]+', $key );
                    if (preg_match('#^'.$key.'$#', $route ) ){
                            $route = preg_replace( '#^'.$key.'$#', $value, $route );
                            return self::$route = $route;
                    }
            }

            return self::$route = $route;

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