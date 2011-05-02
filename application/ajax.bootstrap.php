<?php


        // include the Loader class
        require_once LIBRARY_DIR . "Loader.php";

        // start the loader
        $loader = Loader::get_instance();

        Loader::configure("controller_extension", AJAX_CONTROLLER_EXTENSION );
        Loader::configure("controller_class_name", AJAX_CONTROLLER_CLASS_NAME );

        // enable the ajax mode
        $loader->ajax_mode();

        $loader->init_settings();
        $loader->init_language('en');
        $loader->init_db();
        $loader->auth_user();
        $loader->init_session();
        $loader->load_controller();
        $loader->draw();

// -- end