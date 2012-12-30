<?php

class install_ajax_Controller extends Controller {

    function install() {
        
        //bind password
        $password = post("adminpassword1");
        
        //create important vars
        $salt = rand(0, 99999);
        $md5_password = md5($salt . $password);
        
        
        DB::query("DROP TABLE IF EXISTS ".DB_PREFIX."user");
        DB::query("CREATE TABLE ".DB_PREFIX."user(
                user_id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(90) NOT NULL,
                salt int(5) NOT NULL default '0',
                password varchar(32) NOT NULL default '0',
                email varchar(255) NOT NULL,
                last_ip varchar(255) NOT NULL default '127.0.0.1',
                data_login int(15) NULL,
                status int(1) NOT NULL default 1,
                activation_code varchar(40) NULL,
                lang_id varchar(2) NOT NULL default 'EN',
                PRIMARY KEY (user_id)
                ) ENGINE=MyISAM");
        
        DB::insert(DB_PREFIX . "user", array(
            "name"      => post("adminname"),
            "salt"      => "$salt",
            "password"  => "$md5_password",
            "email"     => post("adminemail", FILTER_SANITIZE_EMAIL),
            "status"    => 3
        ));
        
        DB::query("DROP TABLE IF EXISTS ".DB_PREFIX."user_where_is");
        DB::query("CREATE TABLE ".DB_PREFIX."user_where_is ( 
            user_where_is_id int(11) NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            guest_id int(11) NOT NULL,
            ip varchar(20) NOT NULL default '127.0.0.1',
            name varchar(90) NOT NULL,
            sid varchar(32) NOT NULL,
            url varchar(255) NOT NULL,
            id int(11) NOT NULL,
            file varchar(255) NOT NULL,
            os varchar(255) NOT NULL,
            browser varchar(255) NOT NULL,
            time int(15) NOT NULL , 
            time_first_click int(15) NOT NULL,
            country_code varchar(2) NOT NULL,
            country_name varchar(90) NOT NULL,
            region_code varchar(15) NOT NULL,
            region_name varchar(255) NOT NULL,
            city_name varchar(255) NOT NULL,
            zip varchar(20) NOT NULL,
            latitude varchar(55) NOT NULL,
            longitude varchar(55) NOT NULL,
            timezone_name varchar(255) NOT NULL,
            gmt_offset varchar(255) NOT NULL,
            PRIMARY KEY (user_where_is_id)
            ) ENGINE=MyISAM");
        
        DB::query("DROP TABLE IF EXISTS ".DB_PREFIX."usergroup");
        DB::query("CREATE TABLE ".DB_PREFIX."usergroup(
            group_id int(11) NOT NULL AUTO_INCREMENT,
            parent_id int(11) NOT NULL,
            name varchar(255) NOT NULL,
            position int(11) NOT NULL,
            nuser int(11) NOT NULL,
            PRIMARY KEY (group_id)
            ) ENGINE=MyISAM");
        
        DB::query("DROP TABLE IF EXISTS ".DB_PREFIX."usergroup_user");
        DB::query("CREATE TABLE ".DB_PREFIX."usergroup_user(
            group_id int(11) NOT NULL,
            user_id int(11) NOT NULL,
            PRIMARY KEY (group_id , user_id)
            ) ENGINE=MyISAM");
        
        DB::query("DROP TABLE IF EXISTS ".DB_PREFIX."user_localization");
        DB::query("CREATE TABLE ".DB_PREFIX."user_localization(
            user_localization_id int(11) NOT NULL AUTO_INCREMENT,
            ip varchar(20) NOT NULL DEFAULT '127.0.0.1',
            sid varchar(32) NOT NULL,
            user_id int(11) NOT NULL,
            guest_id int(11) NOT NULL,
            name varchar(90) NOT NULL,
            url varchar(255) NOT NULL,
            id int(11) NOT NULL,
            file varchar(255) NOT NULL,
            os varchar(255) NOT NULL,
            browser varchar(255) NOT NULL,
            time int(15) NOT NULL,
            time_first_click int(15) NOT NULL,
            country_code varchar(2) NOT NULL,
            country_name varchar(90) NOT NULL,
            region_code varchar(15) NOT NULL,
            region_name varchar(255) NOT NULL,
            city_name varchar(255) NOT NULL,
            zip varchar(20) NOT NULL,
            latitude varchar(55) NOT NULL,
            longitude varchar(55) NOT NULL,
            timezone_name varchar(255) NOT NULL,
            gmt_offset varchar(255) NOT NULL,
            PRIMARY KEY (user_localization_id)
            )");
        
        $querys = DB::get_executed_query();
        $html_message = '<br /><strong>Install Complete!!!<br /></strong> Please Delete: <br /><strong>'.CONTROLLERS_DIR.'install
            </strong><br />Please read next steps in the "Install-notes" <br />';
        
        if($querys == 11)
            echo draw_msg($html_message , INFO , true); else echo draw_msg ("ERROR", ERROR, true);
    }

}
