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
class Rain_Group{

    function get_group_list(){
        $db = DB::get_instance();
        return $db->get_list( "SELECT * FROM ".DB_PREFIX."usergroup ORDER BY name", null, "group_id" );
    }

    function get_group( $group_id ){
        $db = DB::get_instance();
        return $db->get_row( "SELECT * FROM ".DB_PREFIX."usergroup WHERE group_id=?", array($group_id) );
    }

    function get_user_in_group( $group_id, $order_by = "name", $order = "asc", $limit = 0 ){
        $db = DB::get_instance();
        return $db->get_list( "SELECT * FROM ".DB_PREFIX."usergroup_user INNER JOIN ".DB_PREFIX."user ON ".DB_PREFIX."usergroup_user.user_id = ".DB_PREFIX."user.user_id WHERE ".DB_PREFIX."usergroup_user.group_id = $group_id ORDER BY $order_by $order" . ($limit>0? " LIMIT $limit" : null ) );
    }

}



?>