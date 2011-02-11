<?php

	// start the session
	session_start();


	
	#--------------------------------
	# Load the class
	#--------------------------------	
	require_once "application/config/constants.php";
	require_once LIBRARY_DIR . "loader.class.php";
	
	


	#--------------------------------
	# Hooks
	hooks('init');
	#--------------------------------

	
	

	#--------------------------------
	# Init Loader class
	#--------------------------------	
	$loader = new Loader;
	$loader->database_connect();		// Connect the database
	$loader->load_settings();			// load the settings
	$loader->set_language('en');		// set the language
	$loader->login();					// do login ( you must pass login=your_login and password=your_password)
	$loader->set_theme('default');		// set theme
	$loader->set_page('index');			// set page layout
	$loader->init_route();				// init the route



	#--------------------------------
	# Auto Load the Controller
	# init_route set the controller/action/params
	# to load the controller
	#--------------------------------
	$loader->auto_load_controller();




	#--------------------------------
	# Load model
	# load the model and assign the result
	# @params model, action, params, assign_to
	#--------------------------------
	$model = 'menu';
	$action = 'load_menu';
	$params = array( $loader->get_selected_controller() );
	$assign_to = 'menu'; // the result will be assigned to template layout "menu"
	$loader->load_model( $model, $action, $params, $assign_to );




	#--------------------------------
	# Assign Layout variables
	#--------------------------------
	$loader->assign( 'title', 'RainFramework' );

	
	
	#--------------------------------
	# Print the layout
	#--------------------------------
	$loader->draw();

	
	
	#--------------------------------
	# Hooks
	hooks('close');
	#--------------------------------

	
?>