<?php

	class Form_Controller extends Controller{

		function index(){

			$options = array( "a"=>1, "b"=>2 );

			require_once LIBRARY_DIR . "form.class.php";
			$form = new Form( URL . "ajax.php/test/" );
			$form->open_table( "Table title" );
			$form->add_hidden( 'a', 1 );
			$form->add_item( 'text', 'name', 'text', 'description', null, 'required' );
			$form->add_item( 'text', 'email', 'email', 'insert your email', null, 'required,email' );
			$form->add_item( 'password', 'pw', 'password', 'insert your pw' );
			$form->add_item( 'select', 'sel', 'select', 'make your choice', null, null, array('options'=>$options) );
			$form->add_item( 'checkbox', 'check', 'select', 'make your choice', null, null, array('options'=>$options) );
			$form->add_item( 'textarea', 'textarea', 'textarea', 'description' );
			$form->add_item( 'word', 'word', 'textarea', 'description' );
			$form->add_item( 'file', 'file', 'filename', 'upload a file' );
			$form->add_item( 'yes', 'yes', 'enable', 'do you want to enable?' );
			$form->add_html( 'this is plain html' );
			$form->add_button();
			$form->close_table();
			$form->draw( $return_string = false, $use_ajax = true );

		}

	}
	
?>