<?php

	/**
	 * 
	 * Enter description here ...
	 * @author federicoulfo
	 *
	 */
	class Form_Controller extends Controller{

		/** 
		 * @var Form 
		 */
		var $form;

		function index(){

			$options = array( "a"=>1, "b"=>2 );
			$this->load_library( "Form", "form" );
			$this->form->init_form( URL . "ajax.php/form/save/", "get" );
			$this->form->open_table( "Table title" );
			$this->form->add_hidden( 'a', 1 );
			$this->form->add_item( 'text', 'name', 'text', 'description', null, 'required' );
			$this->form->add_item( 'text', 'email', 'email', 'insert your email', null, 'required,email' );
			$this->form->add_item( 'password', 'pw', 'password', 'insert your pw' );
			$this->form->add_item( 'select', 'sel', 'select', 'make your choice', null, null, array('options'=>$options) );
			$this->form->add_item( 'checkbox', 'check', 'select', 'make your choice', null, null, array('options'=>$options) );
			$this->form->add_item( 'textarea', 'textarea', 'textarea', 'description' );
			$this->form->add_item( 'word', 'word', 'textarea', 'description' );
			$this->form->add_item( 'file', 'file', 'filename', 'upload a file' );
			$this->form->add_item( 'yes', 'yes', 'enable', 'do you want to enable?' );
			$this->form->add_html( 'this is plain html' );
			$this->form->add_button();
			$this->form->close_table();
			$this->form->draw( $use_ajax = true, $return_string = false );

		}

	}
	
?>