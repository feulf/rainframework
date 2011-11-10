<?php

class Form_Ajax_Controller extends Controller{

	function save(){

		// set the output of the ajax mode
		$this->ajax_mode( true, true );
		echo $name = get_post('name');

	}

}

?>