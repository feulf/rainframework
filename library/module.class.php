<?php

/**
 *	Rain Framework > Module class
 *	-----------------------------
 * 
 * 
 *	@author Federico Ulfo
 *	@copyright developed and mantained by the Rain Team: http://www.raintm.com
 *	@license Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 *	@link http://www.rainframework.com
 *	@package RainFramework
 */



	class Module{

		public $module, $file, $action, $load_area;
		private $params, $selected;

		function Module( $loader, $modules_dir, $module, $file, $action, $params, $load_area, $params, $selected ){
			$this->loader 		= $loader;
			$this->module 		= $module;
			$this->file 		= $file;
			$this->action 		= $action;
			$this->params		= $params;
			$this->load_area 	= $load_area;
			$this->selected		= $selected;
			$this->modules_dir	= $modules_dir;
		}

		function draw(){
			ob_start();
				include( $this->modules_dir . $this->module . "/" . $this->file . ".php" );
				$html = ob_get_contents( );
			ob_end_clean();
			return $html;
		}

		
		function isSelected(){ 
			return $this->isSelected; 
		}

		function par( $key ){
			if( isset( $this->block_param[$key] ) )
				return $this->block_param[$key];
		}

	}



?>