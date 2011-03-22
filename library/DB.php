<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


/**
 * DB class works as interface/alias to the database class: MySql, Sqlite or others
 *
 */
class DB{

	private static $default_link_name = 'default',
                       $config_file   = 'db.php',
                       $config_dir    = CONFIG_DIR,
                       $database_class_dir;

	private	$db,                    // database object
		$link_name = 'default'; // name of the database link object (allows multiple db connection)

	private static $db_list = array(); // array database object



	/**
	 * Initialize the database link
	 *
	 * @param string $link_name  Set the link_name to use different database link connection
	 * @return MySql
	 */
	function __construct( $link_name = null ){
		$this->link_name = $link_name ? $link_name : self::$default_link_name;
		$this->db = isset( db::$db_list[$this->link_name] ) ? db::$db_list[$this->link_name] : null;
	}



	/**
	 * Connect to the database
	 */
	function connect( $hostname = null, $username = null, $password = null, $database = null, $dbserver = 'mysql', $path = null ){

		if( !$hostname && !$username && !$database ){
			require self::$config_dir . self::$config_file;
			extract( $db[$this->link_name] );
		}

                // set the directory
                if( !self::$database_class_dir )
                        self::$database_class_dir = LIBRARY_DIR . "Database/";

                $dbserver = ucfirst(strtolower($dbserver));

		if( file_exists( self::$database_class_dir . $dbserver . ".class.php" ) ){
			require_once self::$database_class_dir . $dbserver . ".class.php";
			$this->db = db::$db_list[$this->link_name] = new $dbserver( $this->link_name );
			$this->db->connect( $hostname, $username, $password, $database, $dbserver, $path );
		}
		else{
			require_once self::$database_class_dir . "pdo.class.php";
			$this->db = db::$db_list[$this->link_name] = new DB_PDO;
			$this->db->connect( $hostname, $username, $password, $database, $dbserver, $path );
		}

	}



	/**
	 * Close mysql connection
	 */
	function disconnect(){
		$this->db->disconnect();
	}



	/**
	 * Execute a write query (insert/update/delete)
	 *
	 */
	function query( $query ){
		return $this->db->query( $query );
	}



	/**
	 * Return the number of rows of the query
	 *
	 * @return int
	 */
	function num_rows( $query = null ){
		return $this->db->num_rows( $query );
	}



	/**
	 * Return the selected field. E.g.:
	 * $name = $db->getField( "name", "SELECT name FROM user LIMIT 1" );
	 *
	 * @return string/int
	 */
	function get_field( $field, $query ){
		return $this->db->get_field( $field, $query );
	}



	/**
	 * Return the selected row as array. E.g.:
	 * $user = $db->getRow( "SELECT * FROM user LIMIT 1" );
	 * // return: array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' )
	 *
	 * @return array/object
	 */
	function get_row( $query = null ){
		return $this->db->get_row( $query );
	}



	/**
	 * Return the selected row as array. E.g.:
	 * $user = $db->getRow( "SELECT * FROM user LIMIT 1" );
	 * // return: array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' )
	 *
	 * @return array/object
	 */
	function get_object( $query = null ){
		return $this->db->get_object( $query );
	}



	/**
	 * Return the selected rows as array. E.g.:
	 * $user_list = $db->getArrayRow( "SELECT * FROM user LIMIT 5" );
	 * // return: $user_list => array( 0 => array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' ), ... , 4 => array( ... ) )
	 *
	 * @return array
	 */
	function get_list( $query = null, $key = null, $value = null ){
		return $this->db->get_list($query,$key,$value);
	}



	/**
	 * Return the selected rows as array. E.g.:
	 * $user_list = $db->getArrayRow( "SELECT * FROM user LIMIT 5" );
	 * // return: $user_list => array( 0 => array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' ), ... , 4 => array( ... ) )
	 *
	 * @return array
	 */
	function get_object_list( $query = null, $key = null ){
		return $this->db->get_object_list($query, $key);
	}



	/**
	 * Return the last inserted id of an insert query
	 */
	function get_inserted_id(){
		return $this->db->get_inserted_id();
	}



	/**
	 * Insert Into
	 * @param array data The parameter must be an associative array (name=>value)
	 */
	function insert( $table, $data ){
		return $this->db->insert( $table, $data );
	}



	/**
	 * Update
	 * @param array data The parameter must be an associative array (name=>value)
	 */
	function update( $table, $data, $where ){
		return $this->db->update( $table, $data, $where );
	}



	/**
	 * Update
	 * @param array data The parameter must be an associative array (name=>value)
	 */
	function delete( $table, $where ){
		return $this->db->delete($table,$where);
	}



	/**
	 * Return the number of executed query
	 */
	function get_executed_query( ){
		return $this->db->get_executed_query();
	}



	/**
	 * Configure the settings
	 *
	 */
	static function configure( $setting, $value ){
		if( is_array( $setting ) )
			foreach( $setting as $key => $value )
                            $this->configure( $key, $value );
		else if( property_exists( "DB", $setting ) )
			self::$$setting = $value;
	}
}

?>
