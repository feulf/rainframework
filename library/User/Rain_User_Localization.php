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
        function init( $id = null, $link, $online_time = USER_ONLINE_TIME ){


                $db = new DB;
		$file 		= basename( $_SERVER['PHP_SELF'] );
		$url 		= $_SERVER['REQUEST_URI'];
		$user_localization = isset( $_SESSION['user_localization'] ) ? $_SESSION['user_localization'] : null;
		$sid 		= session_id();
		$browser	= BROWSER . " " . BROWSER_VERSION;
		$os             = BROWSER_OS;
		$ip 		= IP;

		if( !$user_localization ){
			$time = TIME - HOUR;
			$db->query( "DELETE FROM ".DB_PREFIX."user_user_localization WHERE time < " . HOUR );
		}

		$user_localization_id = $user_localization ? $_SESSION['user_localization']['user_localization_id'] : $db->get_field( "user_localization_id", "SELECT user_localization_id FROM ".DB_PREFIX."user_localization WHERE sid='$sid'" );

		if( $user_id = $this->get_user_id() ){
			$guest_id = 0;
			$name = $this->get_user_field( "name" );
		}
		else{
			$guest_id = isset( $user_localization['guest_id'] ) ? $user_localization['guest_id'] : ( 1 + $db->get_field( "guest_id", "SELECT guest_id FROM ".DB_PREFIX."user_localization ORDER BY guest_id DESC LIMIT 1;" ) );
			$name = _GUEST_ . " " . $guest_id;
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
						$user_localization_id = $db->get_inserted_id();
		}

		$_SESSION['user_localization'] = array( 'user_localization_id' => $user_localization_id, 'id' => $id, 'guest_id'=>$guest_id, 'name'=>$name, 'time' => TIME, 'file' => $file, 'user_id' => $user_id, 'os' => $os, 'browser' => $browser );
        }



	/**
	 * Refresh all the user info
	 */
	function refresh(){
		$db = new DB;
		if( isset( $_SESSION['user_localization'] ) ){
			$db->query( "UPDATE ".DB_PREFIX."user_location SET time='".TIME."' WHERE user_localization_id='{$_SESSION['user_localization']['user_localization_id']}'" );
			$_SESSION['user_localization']['time'] = TIME;
		}
	}



	/**
	 * Get the userWhereIs info
	 */
	function get_user( $user_localization_id, $online_time = USER_ONLINE_TIME ){
		$db = new DB;
		return $db->get_row( "SELECT ".DB_PREFIX."user.*, ".DB_PREFIX."user_location.*
							FROM ".DB_PREFIX."user_location
							LEFT JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_location.user_id = ".DB_PREFIX."user.user_id
							WHERE ( ".TIME." - time ) < $online_time
							AND user_localization_id = $user_localization_id");
	}



	/**
	 * Get the list of all user online
	 */
	function get_user_list( $id = null, $yourself = true, $online_time = USER_ONLINE_TIME ){
		$db = new DB;
		return $db->get_list( 	"SELECT ".DB_PREFIX."user.*, ".DB_PREFIX."user_location.*, IF (".DB_PREFIX."user.user_id > 0, ".DB_PREFIX."user.name, ".DB_PREFIX."user_location.name ) AS name
									FROM ".DB_PREFIX."user_location
									LEFT JOIN ".DB_PREFIX."user ON ".DB_PREFIX."user_location.user_id = ".DB_PREFIX."user.user_id
									WHERE ( ".TIME." - time ) < $online_time
									" . ( $id!=null ? "AND ".DB_PREFIX."user_location.id = $id" : null )
									. ( !$yourself ? " AND ".DB_PREFIX."user_location.sid != '".session_id()."'" : null )
									);
	}



	/**
	 * Delete the user where is info
	 */
	function localization_logout( $user_id ){
		$db = new DB;
		$db->query( "DELETE FROM ".DB_PREFIX."user_location WHERE user_id='$user_id'" );
		unset( $_SESSION['user_localization'] );
	}


}



?>