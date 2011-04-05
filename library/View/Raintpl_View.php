<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */
	require_once "rain.tpl.class.php";

	class Raintpl_view extends Raintpl{
		
		var $cache = null;
		
		function Raintpl_View( $tpl_dir, $cache_dir, $base_url ){
			raintpl::$tpl_dir = $tpl_dir;
			raintpl::$cache_dir = $cache_dir;
			raintpl::$base_url = $base_url;
		}

		function is_cached( $tpl ){
			if( $this->cache = $this->cache( $tpl ) )
				return true;
		}

		function draw( $tpl, $return_string = null ){
			if( $this->cache )
				if( $return_string ) return $this->cache; else echo $this->cache;
			else
				return parent::draw( $tpl, $return_string );
		}

	}


?>