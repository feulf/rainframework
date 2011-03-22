<?php
/* 
 * Init everything
 */

 class Bootstrap_Loader extends Loader{
     
    function init(){

	$this->database_connect();	// Connect the database
	$this->load_settings();		// load the settings
	$this->set_language('en');	// set the language
	$this->login();			// do login ( you must pass login=your_login and password=your_password)
	$this->init_route();		// init the route
	$this->set_theme();		// set theme
        $this->set_page('index');		// set page layout

	#--------------------------------
	# Auto Load the Controller
	# init_route set the controller/action/params
	# to load the controller
	#--------------------------------
	$this->auto_load_controller();



	#--------------------------------
	# Load model
	# load the model and assign the result
	# @params model, action, params, assign_to
	#--------------------------------
	$model = 'menu';
	$action = 'load_menu';
	$params = array( $this->get_selected_controller() );
	$assign_to = 'menu'; // the result will be assigned to template layout "menu"
	$this->load_model( $model, $action, $params, $assign_to );




	#--------------------------------
	# Assign Layout variables
	#--------------------------------
	$this->assign( 'title', 'RainFramework' );



	#--------------------------------
	# Print the layout
	#--------------------------------
	$this->draw();
    }




    function init_ajax(){

        $this->database_connect();	// Connect the database
	$this->load_settings();		// load the settings
	$this->set_language('en');	// set the language
	$this->login();				// do login ( you must pass login=your_login and password=your_password)
	$this->init_route();			// init the route
	$this->set_theme();			// set theme



	#--------------------------------
	# Enable the Ajax Mode
	#--------------------------------
	$this->ajax_mode();

	#--------------------------------
	# Auto Load the Controller
	# init_route set the controller/action/params
	# to load the controller
	#--------------------------------
	$this->auto_load_controller( AJAX_CONTROLLER_EXTENSION, AJAX_CONTROLLER_CLASS_NAME );



	#--------------------------------
	# Print the layout
	#--------------------------------
	$this->draw();

    }


 }


?>
