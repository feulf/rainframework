<?php

        require_once LIBRARY_DIR . "Loader.php";

        $loader = new Loader;
        $loader->database_connect();	// Connect the database
	$loader->init_session();          // init the session
	$loader->load_settings();		// load the settings
	$loader->set_language('en');	// set the language
	$loader->login();				// do login ( you must pass login=your_login and password=your_password)
	$loader->init_route();			// init the route
	$loader->set_theme();			// set theme



	#--------------------------------
	# Enable the Ajax Mode
	#--------------------------------
	$loader->ajax_mode();

	#--------------------------------
	# Auto Load the Controller
	# init_route set the controller/action/params
	# to load the controller
	#--------------------------------
	$loader->auto_load_controller( AJAX_CONTROLLER_EXTENSION, AJAX_CONTROLLER_CLASS_NAME );



	#--------------------------------
	# Print the layout
	#--------------------------------
	$loader->draw();
