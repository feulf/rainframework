<?php

	class View_view{
		
		public $var = array();
		private $static_cache = null, $html_cache = null;
		static $tpl_dir, $cache_dir;
		
		function __construct( $tpl_dir, $cache_dir ){
			self::$tpl_dir = $tpl_dir;
			self::$cache_dir = $cache_dir;
		}
		
		function assign( $variable, $value = null ){

			if( is_array( $variable ) )
				$this->var += $variable;
			elseif( is_object( $variable ) )
				$this->var += (array) $variable;
			else
				$this->var[ $variable ] = $value;

		}

		function is_cache( $tpl, $expire_time = HOUR ){

			$this->static_cache = true;
			$tpl_filename = self::$tpl_dir . $tpl . ".php";
			$cache_filename = self::$cache_dir . $filename;
			if( file_exists($cache_filename) && ( time()-filemtime($cache_filename) ) < $expire_time ){
				$this->html_cache = file_get_contents( $cache_filename );
			}

		}

		function draw( $tpl, $return_string = null ){
			if( $this->html_cache ){
				return $this->html_cache;
			}
			else{
				$tpl_filename = self::$tpl_dir . $tpl . ".php";
				if( file_exists($tpl_filename ) ){
					extract( $this->var );
					ob_start();
					require $tpl_filename;
					$html = ob_get_contents();
					ob_end_clean();
				}else
					die ( "template <b>$tpl_filename</b> not found" );

				if( $this->static_cache ){
					$cache_filename = self::$cache_dir . $filename;
					file_put_contents( $cache_filename, "<?php if(!class_exists('view_view')){exit;}?>" . $html );
				}
				// return or print the template
				if( $return_string ) return $html; else echo $html;

			}
				
		}
		

	}


?>