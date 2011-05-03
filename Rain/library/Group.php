<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



class Group{

        private static $group_obj,
                       $group_class_dir = "User/",
                       $group_class = "Rain_Group";

        function  __construct( $group_list = null ){
		require_once self::$group_class_dir . self::$group_class . '.php';
                self::$group_obj = new self::$group_class;
        }

        /**
         * Get the selected group
         */
        static function get_group( $group_id ){
            return self::$group_obj->get_group( $group_id );
        }

        /**
         * Get the group list
         */
	static function get_group_list(){
            return self::$group_obj->get_group_list();
	}

        /**
         * Get the user into a group
         */
        static function get_user_in_group( $group_id, $order_by = "name", $order = "asc", $limit = 0 ){
            return self::$group_obj->get_user_in_group( $group_id, $order_by, $order, $limit );
        }

	/**
	 * Configure the settings
	 *
	 */
	static function configure( $setting, $value ){
		if( is_array( $setting ) )
			foreach( $setting as $key => $value )
				$this->configure( $key, $value );
		else if( property_exists( "Group", $setting ) )
			self::$$setting = $value;
	}
}



?>