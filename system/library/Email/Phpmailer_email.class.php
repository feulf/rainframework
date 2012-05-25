<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */



require_once "phpmailer/class.phpmailer.php";

/**
 * Interface for PHPMailer
 *
 */
class PHPMailer_Email{

    private $phpmailer;

    function __construct(){
        $this->phpmailer = new PHPMailer();
    }

    function configure( $type, $host, $username, $password, $charset ){
        if( $type == 'mail' )
            $this->phpmailer->isMail();
        elseif( $type == 'smtp' ){
            $this->phpmailer->isSMTP();
            $this->phpmailer->SMTPAuth = true;
        }

        $this->phpmailer->Host = $host;
        $this->phpmailer->Username = $username;
        $this->phpmailer->Password = $password;
        
    }

    function add_sender( $email, $name ){
        $this->phpmailer->from = $email;
        $this->phpmailer->fromName = $name;
    }

    function add_address( $email ){
        $this->phpmailer->AddAddress($email);
    }

    function is_html( $enable = true ){
        $this->phpmailer->isHTML( $enable );
    }

    function set_subject( $subject ){
        $this->phpmailer->Subject=$subject;
    }

    function set_body( $body ){
        $this->phpmailer->Body=$body;
    }

    function add_embedded_image( $path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream' ){
        $this->phpmailer->AddEmbeddedImage( $path, $cid, $name, $encoding, $type );
    }

    function set_alt_body( $alt_body ){
        $this->phpmailer->AltBody=$alt_body;
    }

    function send(){
        $this->phpmailer->Send();
    }




	
}




?>