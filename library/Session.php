<?php


    class Session{

        private $sess_save_path;
        static private $obj_instance;


        private function __construct(){}

        static function get_instance(){
            if( !isset(self::$obj_instance) ){
                self::$obj_instance = new self;
                session_set_save_handler(array(self::$obj_instance,"open"), array(self::$obj_instance,"close"), array(self::$obj_instance,"read"), array(self::$obj_instance,"write"), array(self::$obj_instance,"destroy"), array(self::$obj_instance,"gc") );
                session_start();
            }
            return self::$obj_instance;
        }

        function __set( $name, $value ){
            $_SESSION[$name] = $value;
        }

        function __get( $name ){
            return isset( $_SESSION[$name] ) ? $_SESSION[$name] : null;
        }

        function open($save_path, $session_name){
            $this->sess_save_path = $save_path;
            return true;
        }

        function close(){
            return true;
        }

        function read($id){
            $sess_file = $this->sess_save_path . "/sess_$id";
            if( file_exists($sess_file) )
                return file_get_contents($sess_file);
        }

        function write($id, $sess_data){
            $sess_file = $this->sess_save_path . "/sess_$id";
            return file_put_contents( $sess_file, $sess_data );
        }

        function destroy($id){
            $sess_file = $this->sess_save_path . "/sess_$id";
            if(file_exists($sess_file))
                return unlink($sess_file);
        }

        function gc($maxlifetime){
            foreach (glob("$this->sess_save_path/sess_*") as $filename) {
                if ( file_exists($filename) && filemtime($filename) + $maxlifetime < time())
                    unlink($filename);
            }
            return true;
        }


    }


// -- end