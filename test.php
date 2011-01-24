<?php

	session_start();	

	//----------------------
	// Includes
	//----------------------

	require "inc/functions.php";				// functions	
	require "inc/constants.php";				// constant	
	require "inc/rain.error.php";				// error manager
	require "inc/rain.mysql.class.php";			// mysql
	require "inc/rain.tpl.class.php";			// template
	

	
	timer_start();
	for( $i=0; $i<1000000;$i++)
		$a=TIME;
	echo timer();
	echo "<br>";
	
	timer_start();
	for( $i=0; $i<1000000;$i++)
		$a=time();
	echo timer();
	echo "<br>";
	

	
	
	/*
	//echo '<img src="tmp/xmas.jpg"/>';
	image_resize( TMP_DIR . 'xmas.jpg', TMP_DIR . 'xmas2.jpg', 200, 300, true );
	echo '<br><br>dopo<br><br><img src="tmp/xmas2.jpg"/>';
	image_crop( TMP_DIR . 'xmas.jpg', TMP_DIR . 'xmas3.jpg', 200, 300, 300, 300 );
	echo '<br><br>dopo<br><br><img src="tmp/xmas3.jpg"/>';
	*/
	

?>