<?php

	class Test_Controller extends Controller{

            
                //filter before, automatically called before the action
                function filter_before(){
                    echo "filter before";
                }

                //filter after, automatically called after the action
                function filter_after(){
                    echo "and filter after";
                }
            
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