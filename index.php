<?php

	// start the session
	session_start();




	#--------------------------------
	# Base application directory
	#--------------------------------
        $application = 'application';



        #--------------------------------
	# Load the class
	#--------------------------------
	require_once "$application/config/constants.php";
	require_once "library/Loader.php";
        require_once "$application/Bootstrap.Loader.php";



	#--------------------------------
	# Load the bootstrap
	#--------------------------------
        $loader = new Bootstrap_Loader;
        $loader->init();



?>