<?php

class install_Controller extends Controller {

    function __construct() {
        
    }
    
    function index() {
        
        add_style("box.css");
        
        echo '<div class="content">';
        echo '<h1>Thanks for using Rainframework!</h1>';
        echo 'To Install and Use some Classes you will add some informations about you';
        
        //load form class
        $this->load_library("Form", "form");
        $this->form->init_form(URL . "ajax.php/install/install" , "post");
        $this->form->open_table("Userdata");
        $this->form->add_item("text" , "adminname" , "Admin-Name" , "Enter here your Adminname maybe needed later!!" , null , "required");
        $this->form->add_item("text" , "adminemail" , "Admin-Email" , "for Login" , null , "required,email");
        $this->form->add_item("password" , "adminpassword1" , "Admin-Password" , "for Login" , null , "required" , array("id"=>"adminpass1"));
        $this->form->add_item("password" , "adminpassword2" , "Admin-Password2" , "Repeat your Password" , null , "required");
        $this->form->add_button();
        $this->form->add_validation("adminpassword2" , 'equalTo="#adminpass1"' , "not equalTo Admin-Password");
        $this->form->close_table();
        $this->form->draw($ajax = true , $return_string = false);
        
        echo '<div style="text-align: right; font-size: 0.7em;">';
        echo 'installerversion BETA.1.0';
        echo '</div>';
    }

}