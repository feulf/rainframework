<?php

	class Form{

		function Form( $action = null, $method="get", $name=null, $target=null, $layout = "default", $form_class = DEFAULT_FORM_CLASS ){

			$form_class = $form_class . "_Form";
			require_once APPLICATION_LIBRARY_DIR . "form/" . $form_class . ".class.php";
			$this->form = new $form_class( $action, $method, $name, $target, $layout, $form_class );

		}

		function open_table( $name, $subtitle = null, $class = "table" ){
			$this->form->open_table( $name, $subtitle, $class );
		}

		function close_table( ){
			$this->form->close_table();
		}

		function add_item( $item, $name, $title = null, $description = null, $value = null, $validation = null, $param = null, $layout = null ){
			$this->form->add_item( $item, $name, $title, $description, $value, $validation, $param, $layout );
		}

		function add_hidden( $name, $value ){
    		$this->form->add_hidden( $name, $value );
		}
		
		// wife comment :) nov.12.09
		// hello php...i found youuu. now i work..see? it works now...im sure it does...working...working...working. working hard. im done.  
		function add_html( $html ){
			$this->form->add_html( $html );
		}
		
		//button can be a list or a string
		function add_button( $button = null ){
			$this->form->add_button( $button );
		}
		
		function add_validation( $name, $validation, $message = null ){
			$this->form->add_validation( $name, $validation, $message );
		}

		function draw( $return_string = false, $ajax = false ){
			$this->form->draw( $return_string, $ajax );
		}
		
	}



?>