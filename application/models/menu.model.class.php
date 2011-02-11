<?php

	class Menu_Model extends Model{

		function load_menu( $selected = null){
			$menu = file_list( CONTROLLERS_DIR, "class.php" );
			foreach( $menu as $voice ){
				$voice = array_shift( @explode(".",$voice));
				$menu_list[] = array('name'=>$voice, 'link'=> 'index.php/' . $voice . '/', 'selected' => $selected==$voice );
			}
			return $menu_list;
		}

	}
	
?>