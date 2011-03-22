<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



class User_Localization{

        private static  $user_l10n_obj,
                        $user_l10n_class_dir = "User/",
                        $user_l10n_class = "Rain_User_Localization";

        function  __construct(){
		require_once self::$user_l10n_class_dir . self::$user_l10n_class . '.php';
                self::$user_l10n_obj = new self::$user_l10n_class;
        }

	static function init( $link = null, $id = null, $online_time = USER_ONLINE_TIME ){
            return self::$user_l10n_obj->init( $link, $id, $online_time );
	}

	static function refresh(){
            return self::$user_l10n_obj->refresh();
	}

	static function get_user_localization( $user_localization_id = null, $online_time = USER_ONLINE_TIME ){
            return self::$user_l10n_obj->get_user_localization( $user_localization_id, $online_time );
	}

	static function get_user_localization_list( $id = null, $yourself = true, $online_time = USER_ONLINE_TIME ){
            return self::$user_l10n_obj->get_user_localization_list( $id, $yourself, $online_time);
	}

        static function logout(){
            return self::$user_l10n_obj->logout();
	}

	/**
	 * Configure the settings
	 *
	 */
	static function configure( $setting, $value ){
		if( is_array( $setting ) )
			foreach( $setting as $key => $value )
				$this->configure( $key, $value );
		else if( property_exists( "User_Localization", $setting ) )
			self::$$setting = $value;
	}
}



?>