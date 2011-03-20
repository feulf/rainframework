<?php

	// start the session
	session_start();


        

	#--------------------------------
	# Load the class
	#--------------------------------
	require_once "application/config/constants.php";
	require_once "library/Loader.php";
        require_once "application/Bootstrap.Loader.php";



        $loader = new Bootstrap_Loader;
        $loader->init_ajax();



?>