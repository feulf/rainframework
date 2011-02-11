<?php

	class Menu_Model extends Model{

		function load_menu( $selected = null){
			//$this->menu = file_list( CONTROLLERS_DIR, "class.php" );
			$menu = array( 'content', 'test' );
			foreach( $menu as $voice )
				$menu_list[] = array('name'=>$voice, 'link'=> 'index.php/' . $voice . '/', 'selected' => $selected==$voice );
			return $menu_list;
		}

	}
	
?>