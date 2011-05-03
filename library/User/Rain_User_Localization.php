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
class Rain_User_Localization{

	private $user_localization = null;


	/**
	 * Set the User geolocation and page
	 */
        function init( $link = null, $id = null, $online_time = USER_ONLINE_TIME ){

                $db = DB::get_instance();
		$file 		= basename( $_SERVER['SCRIPT_FILENAME'] );
		$url 		= $_SERVER['REQUEST_URI'];
		$user_localization = isset( $_SESSION['user_localization'] ) ? $_SESSION['user_localization'] : null;
		$sid 		= session_id();
		$browser	= BROWSER . " " . BROWSER_VERSION;
		$os             = BROWSER_OS;
		$ip 		= IP;

		if( !$user_localization ){
			$time = TIME - HOUR;
			$db->query( "DELETE FROM ".DB_PREFIX."user_localization WHERE time < " . HOUR );
		}

		$user_localization_id = $user_localization ? $_SESSION['user_localization']['user_localization_id'] : $db->get_field( "SELECT user_localization_id FROM ".DB_PREFIX."user_localization WHERE sid='$sid'" );

		if( $user_id = User::get_user_id() ){
			$guest_id = 0;
			$name = User::get_user_field( "name" );
		}
		else{
			$guest_id = isset( $user_localization['guest_id'] ) ? $user_localization['guest_id'] : ( 1 + $db->get_field( "SELECT guest_id FROM ".DB_PREFIX."user_localization ORDER BY guest_id DESC LIMIT 1;" ) );
			$name = get_msg('guest') . " " . $guest_id;
		}

		if( $user_localization_id )
			$db->query( "UPDATE ".DB_PREFIX."user_localization SET ip='$ip', user_id='$user_id', name='$name', url='$url', id='$id', file='$file', time='".TIME."', sid='$sid' WHERE user_localization_id='$user_localization_id'" );
		else{

			if( !($location = ip_to_location( $ip, $type = 'array' )) )
				$location = array( 'CountryCode'=>null, 'CountryName'=>null, 'RegionCode'=>null, 'RegionName'=>null, 'City'=>null, 'ZipPostalCode'=>null, 'Latitude'=>null, 'Longitude'=>null, 'TimezoneName'=>null, 'Gmtoffset'=>null );

			//replace_sql_injection( $location );

			$db->query( "INSERT INTO ".DB_PREFIX."user_localization
						(ip,sid,user_id,guest_id,name,url,id,file,os,browser,time,time_first_click,country_code,country_name,region_code,region_name,city_name,zip,latitude,longitude,timezone_name,gmt_offset)
						VALUES
						('$ip','$sid','$user_id','$guest_id','$name','$url','$id','$file','$os','$browser', ".TIME.", ".TIME.", '{$location['CountryCode']}', '{$location['CountryName']}', '{$location['RegionCode']}', '{$location['RegionName']}','{$location['City']}', '{$location['ZipPostalCode']}', '{$location['Latitude']}', '{$location['Longitude']}', '{$location['TimezoneName']}', '{$location['Gmtoffset']}')" );
						$user_localization_id = $db->get_insert_id();
		}

		$_SESSION['user_localization'] = array( 'user_localization_id' => $user_localization_id, 'id' => $id, 'guest_id'=>$guest_id, 'name'=>$name, 'time' => TIME, 'file' => $file, 'user_id' => $user_id, 'os' => $os, 'browser' => $browser );
        }



	/**
	 * Refresh all the user info
	 */
	function refresh(){
		$db = DB::get_instance();
		if( isset( $_SESSION['user_localization'] ) ){
			$db->query( "UPDATE ".DB_PREFIX."user_localization SET time='".TIME."' WHERE user_localization_id='{$_SESSION['user_localization']['user_localization_id']}'" );
			$_SESSION['user_localization']['time'] = TIME;
		}
	}



	/**
	 * Refresh all the user info
	 */
	function get_user_localization_id(){
		if( isset( $_SESSION['user_localization'] ) )
                    return $_SESSION['user_localization']['user_localization_id'];
	}



	/**
	 * Get the userWhereIs info
	 */
	function get_user_localization( $user_localization_id = null, $online_time = USER_ONLINE_TIME ){

                if( !$user_localization_id )
                    $user_localization_id = $this->get_user_localization_id();

		$db = DB::get_instance();
		return $db->get_row( "SELECT ".DB_PREFIX."user.*, ".DB_PREFIX."user_localization.*
							FROM ".DB_PREFIX."user_localization
							LEFT JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_localization.user_id = ".DB_PREFIX."user.user_id
							WHERE ( ".TIME." - time ) < $online_time
							AND user_localization_id = $user_localization_id");
	}



	/**
	 * Get the list of all user online
	 */
	function get_user_localization_list( $id = null, $yourself = true, $online_time = USER_ONLINE_TIME ){
		$db = DB::get_instance();
		return $db->get_list( 	"SELECT ".DB_PREFIX."user.*, ".DB_PREFIX."user_localization.*, IF (".DB_PREFIX."user.user_id > 0, ".DB_PREFIX."user.name, ".DB_PREFIX."user_localization.name ) AS name
					 FROM ".DB_PREFIX."user_localization
					 LEFT JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_localization.user_id = ".DB_PREFIX."user.user_id
					 WHERE ( ".TIME." - time ) < $online_time
					 " . ( $id!=null ? "AND ".DB_PREFIX."user_localization.id = $id" : null )
					 . ( !$yourself ? " AND ".DB_PREFIX."user_localization.sid != '".session_id()."'" : null )
                                    );
	}



	/**
	 * Delete the user where is info
	 */
	function logout( $user_localization_id = null ){

                if( !$user_localization_id )
                    $user_localization_id = $this->get_user_localization_id();

		$db = DB::get_instance();
		$db->query( "DELETE FROM ".DB_PREFIX."user_localization WHERE user_localization_id='$user_localization_id'" );
		unset( $_SESSION['user_localization'] );
	}


}



?>