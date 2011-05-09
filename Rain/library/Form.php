<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */

	class Form{

                private static $form_class_dir = "Form/",
                               $form_class = "Rain_Form";

                private $form_obj;
		
		function init_form( $action = null, $method="get", $name=null, $target=null, $layout = "default" ){
			require_once self::$form_class_dir . self::$form_class . ".php";
			$this->form_obj = new self::$form_class( $action, $method, $name, $target, $layout );
		}

		function open_table( $name, $subtitle = null, $class = "table" ){
			$this->form_obj->open_table( $name, $subtitle, $class );
		}

		function close_table( ){
			$this->form_obj->close_table();
		}

		function add_item( $item, $name, $title = null, $description = null, $value = null, $validation = null, $param = null, $layout = null ){
			$this->form_obj->add_item( $item, $name, $title, $description, $value, $validation, $param, $layout );
		}

		function add_hidden( $name, $value ){
                        $this->form_obj->add_hidden( $name, $value );
		}
		
		// wife comment :) nov.12.09
		// hello php...i found youuu. now i work..see? it works now...im sure it does...working...working...working. working hard. im done.  
		function add_html( $html ){
			$this->form_obj->add_html( $html );
		}
		
		//button can be a list or a string
		function add_button( $button = null ){
			$this->form_obj->add_button( $button );
		}
		
		function add_validation( $name, $validation, $message = null ){
			$this->form_obj->add_validation( $name, $validation, $message );
		}

		function draw( $ajax = false, $return_string = false ){
			return $this->form_obj->draw( $ajax, $return_string );
		}

                /**
                 * Configure the settings
                 *
                 */
                static function configure( $setting, $value ){
                        if( is_array( $setting ) )
                                foreach( $setting as $key => $value )
                                        $this->configure( $key, $value );
                        else if( property_exists( "Form", $setting ) )
                                self::$$setting = $value;
                }
		
	}



?>