<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



class User{

        private static  $user_obj,
                        $library_dir = LIBRARY_DIR,
                        $user_class_dir = "User/",
                        $user_class = "Rain_User";

        function  __construct(){
		require_once self::$library_dir . self::$user_class_dir . self::$user_class . '.php';
                self::$user_obj = new self::$user_class;
        }

        /**
         * Log the user and return the login level: LOGIN_LOGOUT, LOGIN_WAIT, LOGIN_ERROR, LOGIN_BANNED, LOGIN_NOT_LOGGED, LOGIN_DONE, LOGIN_LOGGED
         * If enable_cookies it save login/pw (crypted md5($salt.$password) to log automatically
         */
	static function login( $login = null, $password = null, $enable_cookies = false, $logout = null, $errorWait = 5 ){
            return self::$user_obj->login( $login, $password, $enable_cookies, $logout, $errorWait );
	}

        /**
         * do logout
         */
	static function logout(){
            self::$user_obj->logout();
	}

	/**
         * get the user_id
         */
	static function get_user_id(){
            return self::$user_obj->get_user_id();
	}

        /**
         * refresh the user information
         */
	static function refresh_user_info(){
            self::$user_obj->refresh_user_info();
	}

        /**
         * return the user info of the selected $user_id.
         * If $user_id
         */
	static function get_user( $user_id = null ){
            return self::$user_obj->get_user($user_id);
	}

	static function is_admin( $user_id = null ){
            return self::$user_obj->is_admin($user_id);
	}

	static function is_super_admin( $user_id = null ){
            return self::$user_obj->is_super_admin($user_id);
	}

	static function get_user_field( $field, $user_id = null ){
            return self::$user_obj->get_user_field($field, $user_id);
	}

	static function set_user_lang( $lang_id ){
            return self::$user_obj->set_user_lang($lang_id);
	}

	static function user_where_is_init( $id, $link, $online_time = USER_ONLINE_TIME ){
            return self::$user_obj->user_where_id_init( $id, $link, $online_time );
	}

	static function user_where_is_refresh(){
            return self::$user_obj->user_where_is_refresh();
	}

	static function get_user_where_is_user( $user_where_is_id, $online_time = USER_ONLINE_TIME ){
            return self::$user_obj->get_user_where_is_user( $user_where_is_id, $online_time );
	}

	static function get_user_where_is_list( $id = null, $yourself = true, $online_time = USER_ONLINE_TIME ){
            return self::$user_obj->get_user_where_is_list( $id, $yourself, $online_time);
	}

	static function get_user_where_is( ){
            return self::$user_obj->get_user_where_is();
	}

        static function user_where_is_logout( $user_id ){
            return self::$user_obj->user_where_is_logout($user_id);
	}

        static function get_user_group_list( ){
            return self::$user_obj->get_user_group_list();
        }

	/**
	 * Configure the settings
	 *
	 */
	static function configure( $setting, $value ){
		if( is_array( $setting ) )
			foreach( $setting as $key => $value )
				$this->configure( $key, $value );
		else if( property_exists( __CLASS__, $setting ) )
			self::$$setting = $value;
	}
}



// -- end