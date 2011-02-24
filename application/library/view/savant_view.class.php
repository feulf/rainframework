<?php


	require_once "Savant3.php";

	class Savant_view extends Savant3{
		
		var $cache = null;
		static $tpl_dir, $cache_dir, $base_url;
		
		function Smarty_View( $tpl_dir, $cache_dir, $base_url ){
			self::$tpl_dir = $tpl_dir;
			self::$cache_dir = $cache_dir;
			self::$base_url = $base_url;
			
			Savant::parent();
		}

		// savant doesn't implement cache
		function is_cache( $tpl ){
			return false;
		}

		// draw
		function draw( $tpl, $return_string = null ){
			echo "doesn't work because the templates in this example are for raintpl<br>";
			parent::display( VIEWS_DIR . self::$tpl_dir . $tpl . ".php" );
		}

	}


?>