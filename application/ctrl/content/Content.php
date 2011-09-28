<?php

	class Content_Controller extends Controller{
            
		function index(){
phpinfo();
exit;
			
			$v = get_post();
			dump( $v );
			
			//love easy
			$this->load_model("content","content_obj");

			$content_row = $this->content_obj->get();

			// Load view
			$tpl = new View;
			$tpl->assign( $content_row );
			$tpl->draw('content/content');

		}

	}
	
?>