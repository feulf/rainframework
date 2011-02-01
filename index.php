<?php

	// start the session
	session_start();

	// Load the class
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
	$loader->load_model( "menu", "load_menu", null, "menu");



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