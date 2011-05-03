<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



/**
 * Load and draw templates
 *
 */
class Rain_User{

	private static $user;

	// do login
	function login( $login = null, $password = null, $enable_cookies = false, $logout = null, $errorWait = 1 ){

		if( $logout )
			return LOGIN_LOGOUT;

		// true if the user is logged
		// In shared server could happen that your login is shared in all website, user.check verify that the login is only on this application
		elseif( !$login && !$password && isset( $_SESSION['user'] ) && isset( $_SESSION['user']['check'] ) && $_SESSION['user']['check'] == BASE_DIR ){
			self::$user = $_SESSION['user'];
			return LOGIN_LOGGED;
		}
		else
			$_SESSION['user'] = null;

		//se login e password sono salvate nei cookie
		if( isset($_COOKIE['login']) AND isset($_COOKIE['password']) ){
			$login = $_COOKIE['login'];
			$salt_and_pw = $_COOKIE['password'];
		}
		else
			$salt_and_pw = null;

		//check if there's login and pw, or salt_pw
		if( $login AND ($password OR $salt_and_pw) ){

			$db = DB::get_instance();
			if( !$salt_and_pw )
				$salt_and_pw = md5( $db->get_field( "SELECT salt FROM ".DB_PREFIX."user WHERE email = '{$login}'" ) . $password );

			if( $user = $db->get_row( "SELECT * FROM ".DB_PREFIX."user WHERE email = '$login' AND password = '$salt_and_pw'" ) ){

				// create new salt and password
				if( $password ){
					$user_id = $user['user_id'];
					$salt=rand( 0, 99999 );
					$md5_password = md5( $salt . $password );
					$db->query( "UPDATE ".DB_PREFIX."user SET password='$md5_password', salt='$salt', activation_code='' WHERE user_id='$user_id'" );
				}

				if( $enable_cookies ){
					setCookie( "login", $login, time( ) + $one_year = 60*60*24*31*12 );
					setCookie( "password", $salt_and_pw, time( ) + $one_year );
				}

				$user['check'] = $_SESSION['user']['check'] = BASE_DIR;
				$user['level'] = get_msg( strtolower($GLOBALS['user_level'][ $user['status'] ]) );

				// save user data
				self::$user = $_SESSION['user'] = $user;

				//update date and IP
				$db->query( "UPDATE ".DB_PREFIX."user SET last_ip='".get_ip()."', data_login=UNIX_TIMESTAMP() WHERE user_id='{$user['user_id']}'" );

				return LOGIN_DONE;
			}
			else{

				// if login is wrong PHP will sleep for $errorWait seconds
				sleep( $errorWait );
				self::$user = null;
				unset($_SESSION['user']);
				setcookie ("login", "", time() - 3600);
				setcookie ("password", "", time() - 3600);

				return LOGIN_ERROR;
			}
		}
		else
			return LOGIN_NOT_LOGGED;
	}

	function logout(){
		if( $user_id = get_user_id() )
                    $this->user_where_is_logout( $user_id );
		self::$user = null;
		unset($_SESSION['user']);
		setcookie ("login", "", time() - 3600);
		setcookie ("password", "", time() - 3600);
	}


	// return 0 if not logged
	function get_user_id(){
                return isset(self::$user) ? self::$user['user_id'] : null;
	}


	function refresh_user_info(){
		$db = DB::get_instance();
		self::$user = $_SESSION['user'] = $this->get_user();
		self::$user['check'] = $_SESSION['user']['check'] = BASE_DIR;
		return self::$user;
	}

	function get_user($user_id=null){
		if( $user_id ){
			$db = DB::get_instance();
			$user = $db->get_row( "SELECT * FROM ".DB_PREFIX."user WHERE user_id = '{$user_id}'" );
			$user['level'] = get_msg( strtolower($GLOBALS['user_level'][ $user['status'] ]) );
			return $user;
		}
		else
			return isset( self::$user ) ? self::$user : null;
	}


	function is_admin( $user_id = NULL ){
		return $this->get_user_field( "status", $user_id ) >= USER_ADMIN;
	}



	/**
	 * return true if the user is super admin
	 * @param int $user_id By default is selected the logged user
	 */
	function is_super_admin( $user_id = NULL ){
		return $this->get_user_field( "status", $user_id ) >= USER_SUPER_ADMIN;
	}



	/**
	 * Select the field of the user
	 * @param string $field Selected field
	 * @param int $user_id By default is selected the logged user
	 */
	function get_user_field( $field, $user_id = NULL ){
		if( $user = $this->get_user( $user_id ) ){
			if( isset( $user[$field] ) )
				return $user[$field];
			else
				trigger_error( "Field not found: $field" );
		}
	}



	/**
	 * set the language on the selected user
	 */
	function set_user_lang( $lang_id ){
		if( $user_id=$this->get_user_id() ){
			$db = DB::get_instance();
			$db->query( "UPDATE ".DB_PREFIX."user SET lang_id='{$lang_id}' WHERE user_id={$user_id}" );
			$_SESSION['user']['lang_id']=$lang_id;
		}
	}



	/**
	 * Set the User geolocation and page
	 */
	function user_where_is_init( $id, $link, $online_time = USER_ONLINE_TIME ){
		$db = DB::get_instance();
		$file 		= basename( $_SERVER['PHP_SELF'] );
		$url 		= $_SERVER['REQUEST_URI'];
		$where_is 	= isset( $_SESSION['where_is'] ) ? $_SESSION['where_is'] : null;
		$sid 		= session_id();
		$browser	= BROWSER . " " . BROWSER_VERSION;
		$os			= BROWSER_OS;
		$ip 		= get_ip();

		if( !$where_is ){
			$time = TIME - HOUR;
			$db->query( "DELETE FROM ".DB_PREFIX."user_where_is WHERE time < " . HOUR );
		}

		$user_where_is_id = $where_is ? $_SESSION['where_is']['user_where_is_id'] : $db->get_field( "SELECT user_where_is_id FROM ".DB_PREFIX."user_where_is WHERE sid='$sid'" );

		if( $user_id = $this->get_user_id() ){
			$guest_id = 0;
			$name = $this->get_user_field( "name" );
		}
		else{
			$guest_id = isset( $where_is['guest_id'] ) ? $where_is['guest_id'] : ( 1 + $db->get_field( "SELECT guest_id FROM ".DB_PREFIX."user_where_is ORDER BY guest_id DESC LIMIT 1;" ) );
			$name = _GUEST_ . " " . $guest_id;
		}

		if( $user_where_is_id )
			$db->query( "UPDATE ".DB_PREFIX."user_where_is SET ip='$ip', user_id='$user_id', name='$name', url='$url', id='$id', file='$file', time='".TIME."', sid='$sid' WHERE user_where_is_id='$user_where_is_id'" );
		else{

			if( !($location = ip_to_location( $ip, $type = 'array' )) )
				$location = array( 'CountryCode'=>null, 'CountryName'=>null, 'RegionCode'=>null, 'RegionName'=>null, 'City'=>null, 'ZipPostalCode'=>null, 'Latitude'=>null, 'Longitude'=>null, 'TimezoneName'=>null, 'Gmtoffset'=>null );

			$db->query( "INSERT INTO ".DB_PREFIX."user_where_is
						(ip,sid,user_id,guest_id,name,url,id,file,os,browser,time,time_first_click,country_code,country_name,region_code,region_name,city_name,zip,latitude,longitude,timezone_name,gmt_offset)
						VALUES
						('$ip','$sid','$user_id','$guest_id','$name','$url','$id','$file','$os','$browser', ".TIME.", ".TIME.", '{$location['CountryCode']}', '{$location['CountryName']}', '{$location['RegionCode']}', '{$location['RegionName']}','{$location['City']}', '{$location['ZipPostalCode']}', '{$location['Latitude']}', '{$location['Longitude']}', '{$location['TimezoneName']}', '{$location['Gmtoffset']}')" );
						$user_where_is_id = $db->get_insert_id();
		}

		$_SESSION['where_is'] = array( 'user_where_is_id' => $user_where_is_id, 'id' => $id, 'guest_id'=>$guest_id, 'name'=>$name, 'time' => TIME, 'file' => $file, 'user_id' => $user_id, 'os' => $os, 'browser' => $browser );
	}



	/**
	 * Refresh all the user info
	 */
	function user_where_is_refresh(){
		$db = DB::get_instance();
		if( isset( $_SESSION['where_is'] ) ){
			$db->query( "UPDATE ".DB_PREFIX."user_where_is SET time='".TIME."' WHERE user_where_is_id='{$_SESSION['where_is']['user_where_is_id']}'" );
			$_SESSION['where_is']['time'] = TIME;
		}
	}



	/**
	 * Get the userWhereIs info
	 */
	function get_user_where_is_user( $user_where_is_id, $online_time = USER_ONLINE_TIME ){
		$db = DB::get_instance();
		return $db->get_row( "SELECT ".DB_PREFIX."user.*, ".DB_PREFIX."user_where_is.*
							FROM ".DB_PREFIX."user_where_is
							LEFT JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_where_is.user_id = ".DB_PREFIX."user.user_id
							WHERE ( ".TIME." - time ) < $online_time
							AND user_where_is_id = $user_where_is_id");
	}



	/**
	 * Get the list of all user online
	 */
	function get_user_where_is_list( $id = null, $yourself = true, $online_time = USER_ONLINE_TIME ){
		$db = DB::get_instance();
		return $db->get_list( 	"SELECT ".DB_PREFIX."user.*, ".DB_PREFIX."user_where_is.*, IF (".DB_PREFIX."user.user_id > 0, ".DB_PREFIX."user.name, ".DB_PREFIX."user_where_is.name ) AS name
									FROM ".DB_PREFIX."user_where_is
									LEFT JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_where_is.user_id = ".DB_PREFIX."user.user_id
									WHERE ( ".TIME." - time ) < $online_time
									" . ( $id!=null ? "AND ".DB_PREFIX."user_where_is.id = $id" : null )
									. ( !$yourself ? " AND ".DB_PREFIX."user_where_is.sid != '".session_id()."'" : null )
									);
	}



	/**
	 * Get the info of the logged user
	 */
	function get_user_where_is( ){
		return $where_is = isset( $_SESSION['where_is'] ) ? $_SESSION['where_is'] : null;
	}



	/**
	 * Delete the user where is info
	 */
	function user_where_is_logout( $user_id ){
		$db = DB::get_instance();
		$db->query( "DELETE FROM ".DB_PREFIX."user_where_is WHERE user_id='$user_id'" );
		unset( $_SESSION['where_is'] );
	}

	/**
	 * get the group list
	 */
	function get_user_group_list(){
                if( $user_id = $this->get_user_id() ){
                    $db = DB::get_instance();
                    return $db->get_list(   "SELECT *
                                            FROM ".DB_PREFIX."usergroup AS g
                                            JOIN ".DB_PREFIX."usergroup_user AS gu ON g.group_id=gu.group_id
                                            WHERE gu.user_id=?
                                            ORDER BY name",
                                            array($group_id));
                }
	}


}



?>