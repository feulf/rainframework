<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


/**
 * Class for MySql database management
 */

class DB_PDO{

	/**
	 * Set true if you want to exit on Query error
	 */
	public static 	$exit_on_error = true;

	private	$result,		// result of the query
		$link,			// database link
		$link_name = 'default';	// name of the database link

	private static	$nquery = 0,		// count the query executed
			$link_array = array();	// array of links



	/**
	 * Initialize the database link
	 *
	 * @param string $link_name  Set the link_name to use different database link connection
	 * @return MySql
	 */
	function DB_PDO(){
		$this->link = isset( DB_PDO::$link_array[$this->link_name] ) ? DB_PDO::$link_array[$this->link_name] : null;
	}



	/**
	 * Connect to the database
	 */
	function connect( $hostname = null, $username = null, $password = null, $database = null, $dbserver = 'mysql', $database_path = null ){

		if( $this->link ) // if already connected return true
			return true;

		if( !$hostname && !$username && !$database ){
			require CONFIG_DIR . "conf.db.php";
			extract( $db[$this->link_name] );
		}
		try{
			
			switch( $dbserver ){
				case 'mysql':
				case 'pgsql':
			    	$this->link = DB_PDO::$link_array[$this->link_name] = new PDO( "$dbserver:host=$hostname;dbname=$database", $username, $password );
			    break;
				case 'sqlite':
					$this->link = DB_PDO::$link_array[$this->link_name] = new PDO( "sqlite:$database_path" );
				break;
				case 'oracle':
					$this->link = DB_PDO::$link_array[$this->link_name] = new PDO( "OCI:", $username, $password );
				break;
				case 'odbc':
					$this->link = DB_PDO::$link_array[$this->link_name] = new PDO( "odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq={$database_path};Uid={$username}");
				break;
				default:
					die( "DBMS $dbserver not found" );
			}
		} catch (PDOException $e) {
			die( "Error!: " . $e->getMessage() . "<br/>" );
		}

	}



	/**
	 * Close mysql connection
	 */
	function disconnect( ){
		unset( DB_PDO::$link_array[$this->link_name] );
	}
	


	/**
	 * Execute a write query (insert/update/delete)
	 * 
	 */
	function query( $query ){
		try{
			$this->link->exec( $query );
		} catch ( PDOException $e ){
			error_reporting( "Error!: " . $e->getMessage() . "<br/>", E_USER_ERROR );
		}
	}



	/**
	 * Return the number of rows of the query
	 * 
	 * @return int
	 */
	function num_rows( $query = null ){
		if( $res = $this->link->query( $query ) )
			return $res->fetch(PDO::FETCH_ASSOC);
	}



	/**
	 * Return the selected field. E.g.:
	 * $name = $db->getField( "name", "SELECT name FROM user LIMIT 1" );
	 * 
	 * @return string/int
	 */
	function get_field( $field, $query ){
		if( $row = $this->get_row( $query ) )
			return $row[$field];
	}
	
	
	
	/**
	 * Return the selected row as array. E.g.:
	 * $user = $db->getRow( "SELECT * FROM user LIMIT 1" ); 
	 * // return: array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' )
	 * 
	 * @return array
	 */
	function get_row( $query = null ){
		return $this->link->query( $query )->fetch(PDO::FETCH_ASSOC);
	}



	/**
	 * Return the selected rows as array. E.g.:
	 * $user_list = $db->getArrayRow( "SELECT * FROM user LIMIT 5" );
	 * // return: $user_list => array( 0 => array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' ), ... , 4 => array( ... ) )
	 * 
	 * @return array
	 */
	function get_list( $query = null, $key = null, $value = null ){
		
		if( $res = $this->link->query( $query )->fetchALL(PDO::FETCH_ASSOC ) ){
			if( !$key )
				return $res;
			elseif( !$value )
				foreach( $res as $row )
					$rows[ $row[$key] ] = $row;
			else
				foreach( $res as $row )
					$rows[ $row[$key] ] = $row[$value];
			
			return $rows;
		}
	}



	/**
	 * Return the selected row as object. E.g.:
	 * $user = $db->get_row( "SELECT * FROM user LIMIT 1" );
	 * // you can access as $user->name
	 *
	 * @return array
	 */
	function get_object( $query = null ){
		return $this->link->query( $query )->fetchObject();
	}



	/**
	 * Return the selected rows as object list . E.g.:
	 * $user_list = $db->getArrayRow( "SELECT * FROM user LIMIT 5" );
	 * // return: $user_list => array( 0 => obj $user,  )
	 *
	 * @return array
	 */
	function get_object_list( $query = null, $key = null ){
		if( $res = $this->link->query( $query )->fetchALL(PDO::FETCH_CLASS ) ){
			if( $key )
				foreach( $res as $row )
					$rows[$row->$key] = $row;
			else
				foreach( $res as $row )
					$rows[] = $row;
		}
		return isset($rows)?$rows:null;
	}



	/**
	 * Return the last inserted id of an insert query
	 */
	function get_inserted_id( ){
		return $this->link->lastInsertedId();
	}
    
	
    
	/**
	 * Insert Into
	 * @param array data The parameter must be an associative array (name=>value)
	 */
	function insert( $table, $data ){
		if( count( $data ) ){
			$fields = $values = "";
				foreach( $data as $name => $value ){
					$fields .= $fields ? ",`$name`" : "`$name`";
					$values .= $values ? ",`$value`" : "`$value`";
				}
			return $this->query( "INSERT INTO $table ($fields) VALUES ($values)" );
		}
	}



	/**
	 * Update
	 * @param array data The parameter must be an associative array (name=>value)
	 */
	function update( $table, $data, $where ){
		if( count( $data ) ){
			$fields = "";
			foreach( $data as $name => $value )
				$fields .= $fields ? ",`$name`='$value'" : ",`$name`='$value'";
			$where = is_string( $where ) ? " WHERE $where" : null;
			return $this->query("UPDATE $table SET $fields $where");
		}
	}
    
    
    
	/**
	 * Update
	 * @param array data The parameter must be an associative array (name=>value)
	 */
	function delete( $table, $where ){
		return $this->query("DELETE $table where $where");
	}
	
	
	
	/**
	 * Return the number of executed query
	 */
	function get_executed_query( ){
		return DB_PDO::$nquery;
	}

}

?>
