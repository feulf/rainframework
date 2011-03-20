<?php

	class Test_Controller extends Controller{

		function index(){

			//love easy
			echo draw_msg( 'this is a test' );

		}
		
		function ajax_index(){
			$this->ajax_mode( true, true );
			echo draw_msg( 'hei' );
		}

	}
	
?>