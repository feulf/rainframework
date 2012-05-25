<?php

	class Language{

		protected	$language = "en",
					$language_dir = LANGUAGE_DIR;

		function set_language( $language = DEFAULT_LANGUAGE ){
			$this->language = $language;
			require_once LANGUAGE_DIR . $language . "/generic.php";
			define( "LANG_ID", $language );
		}

		function get_language( ){
			return $this->language;
		}

		function load_dictionary( $dictionary ){
			require_once self::$language_dir . $this->language . "/" . $dictionary . ".php";
		}

		static function get_installed_languages( $only_published = true ){
			return DB::get_all( "SELECT * FROM ".DB_PREFIX."language" . ( $only_published ? " WHERE published=1" : null ) );
		}

	}


// -- end