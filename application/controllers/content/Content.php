<?php

	class Content_Controller extends Controller{

		function index(){

                    $db = db::get_instance();
$array_team = array('lastname' => "eeeeeeeeee");
$db->update("rain_user", $array_team, "user_id = 1");
                    
                    
			//love easy
			$this->load_model("content","content_obj");

			$content_row = $this->content_obj->get();
			
			// Load view
			$tpl = new View;
			$tpl->assign( $content_row );
			$tpl->draw('content/content' );

		}

	}
	
?>