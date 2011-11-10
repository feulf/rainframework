<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



	class Model{

		function load_model($model,$object_name=null){
			
			/* --------------- ATTENTION -------------------
			 *  
			 *    Cette fonction existe AUSSI dans la classe Controller !!!!
			 * 
			 * -------------------------------------------- */

			 // transform the model string to capitalized. e.g. user => User, news_list => News_List
			$model = implode( "/", array_map( "ucfirst", array_map( "strtolower", explode( "/", $model ) ) ) );
			$model = implode( "_", array_map( "ucfirst",  explode( "_", $model )  ) );


			// include the file

			if( file_exists($file = self::$models_dir . $model . ".php") ) {
				require_once $file;
			}
			else{

				trigger_error( "MODEL: FILE <b>{$file}</b> NOT FOUND ", E_USER_WARNING );
				return false;
			}

			if(!$object_name)
				$object_name = $model;

			$tModel = explode("/",$model);
			$class=$tModel[count($tModel)-1];
			$class.="_Model";

			//$class = ($real_name)?$real_name."_Model":$model . "_Model";

			if( class_exists($class) ){
				$this->$object_name = new $class;

			}
			else{

				trigger_error( "MODEL: CLASS <b>{$model}</b> NOT FOUND", E_USER_WARNING );
				return false;
			}
			return true;
		}


	}



?>