<?php


	require_once "rain.tpl.class.php";

	class Raintpl_view extends Raintpl{
		
		var $cache = null;
		
		function Raintpl_View( $tpl_dir, $cache_dir, $base_url ){
			raintpl::$tpl_dir = $tpl_dir;
			raintpl::$cache_dir = $cache_dir;
			raintpl::$base_url = $base_url;
		}

		function draw( $tpl, $return_string = null ){
			if( $this->cache )
				return $this->html;
			else
				return parent::draw( $tpl, $return_string );
		}
		
		function is_cached( $tpl, $expire_time = HOUR ){
			if( $this->cache = $this->cache( $tpl, $expire_time ) )
				return true;
		}

	}


?>