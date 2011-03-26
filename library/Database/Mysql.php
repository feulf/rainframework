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

class MySql{

	/**
	 * Set true if you want to exit on Query error
	 */
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
	function MySql( $link_name = null ){
		if( !$link_name )
			$this->link_name = $link_name;
		$this->link = isset( mysql::$link_array[$this->link_name] ) ? mysql::$link_array[$this->link_name] : null;
	}



	/**
	 * Connect to the database
	 */
	function connect( $hostname = null, $username = null, $password = null, $database = null ){
		
		if( $this->link ) // if already connected return true
			return true;
			
		if( !$hostname && !$username && !$database )
			require CONFIG_DIR . "db.php";

		if( $this->link = mysql::$link_array[$this->link_name] = mysql_connect( $hostname, $username, $password ) or die( mysql_error() ) )
	    	return mysql_select_db( $database ) or die ( mysql_error() );
	}



	/**
	 * Close mysql connection
	 */
	function disconnect( ){
		return mysql_close( $this->link );
	}
	


	/**
	 * Execute query. Use this function for update/delete query, for read query use getField, getRow, getArrayRow ...
	 * 
	 * @return bool
	 */
	function query( $query ){
		
		if( ( $query || $query=$this->query ) && !isset( $this->result[$query] ) ){
			if( $this->result[$query] = mysql_query( $query, $this->link ) ){	
				mysql::$nquery++;
				return $this->result[ $this->query = $query ];
			}
			else{
        		trigger_error( mysql_error($this->link) . "<br/><font color=\"red\">$query</font><br/>", E_USER_ERROR );
        		// if debug mode on query error stop the execution
	        	if( isset($GLOBALS['debug']) && $GLOBALS['debug'] == true )
		        	exit;
			}
		}
		else
			return $this->result[ $query ];
	}



	/**
	 * Return the number of rows of the query
	 * 
	 * @return int
	 */
	function num_rows( $query = null ){
		if( $result = $this->query( $query ) )
			return mysql_num_rows( $result );
	}



	/**
	 * Return the selected field. E.g.:
	 * $name = $db->getField( "name", "SELECT name FROM user LIMIT 1" );
	 * 
	 * @return string/int
	 */
	function get_field( $field, $query = null ){
		if( $row = $this->get_row( $query ) and isset( $row[$field] ) )
			return $row[$field];

	}
	
	
	
	/**
	 * Return the selected row as array. E.g.:
	 * $user = $db->get_row( "SELECT * FROM user LIMIT 1" ); 
	 * // return: array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' )
	 * 
	 * @return array
	 */
	function get_row( $query = null ){
		return mysql_fetch_assoc( $this->query( $query ) );
	}




	/**
	 * Return the selected rows as array. E.g.:
	 * $user_list = $db->getArrayRow( "SELECT * FROM user LIMIT 5" );
	 * // return: $user_list => array( 0 => array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' ), ... , 4 => array( ... ) )
	 * 
	 * @return array
	 */
	function get_list( $query = null, $key = null, $value = null ){
		if( $key && $value )
			while( $row = mysql_fetch_assoc( $this->query($query) ) )
				$rows[ $row[$key] ] = $row[$value];
		
		elseif( $key )
			while( $row = mysql_fetch_assoc( $this->query($query) ) )
				$rows[ $row[$key] ] = $row;
		
		else
			while( $row = mysql_fetch_assoc( $this->query( $query ) ) )
				$rows[ ] = $row;
		
		return isset($rows)?$rows:null;
	}




	/**
	 * Return the selected row as object. E.g.:
	 * $user = $db->get_row( "SELECT * FROM user LIMIT 1" );
	 * // you can access as $user->name
	 *
	 * @return array
	 */
	function get_object( $query = null ){
		return mysql_fetch_object( $this->query( $query ) );
	}



	/**
	 * Return the selected rows as object list . E.g.:
	 * $user_list = $db->getArrayRow( "SELECT * FROM user LIMIT 5" );
	 * // return: $user_list => array( 0 => obj $user,  )
	 *
	 * @return array
	 */
	function get_object_list( $query = null, $key = null ){
		if( $key )
			while( $row = mysql_fetch_object( $this->query( $query ) ) )
				$rows[$row->$key] = $row;
		else
			while( $row = mysql_fetch_object( $this->query( $query ) ) )
				$rows[ ] = $row;
		return isset($rows)?$rows:null;
	}




	/**
	 * Return the last inserted id of an insert query
	 */
	function get_inserted_id( ){
		return mysql_insert_id( $this->link );
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
	* Call this method at begining to profile the queries
	*/
	function setProfiling(){
		$this->query("SET profiling=1");
	}
	
	
	
	/**
	 * Return the number of executed query
	 */
	function get_executed_query( ){
		return mysql::$nquery;
	}



	/**
	* Call this method at end to get the profile
	*/
	function showProfile(){
		if( $profiles = $this->getArrayRow( "SHOW profiles" ) ){
			$html = '<table cellspacing="1" cellpadding="10" bgcolor="#cccccc" style="font:11px Helvetica;"><tr style="font-weight:bold"><td>Query ID</td><td>exec time</td><td width="150">%</td><td>Query</td></tr>';
			for( $i=0, $execution_time = 0, $n=count($profiles); $i<$n; $i++ )
				$execution_time += $profiles[$i]['Duration'];

			foreach($profiles as $i => $p ){
				$perc = round( ( $p['Duration'] / $execution_time ) * 100, 2 );
				$width = ceil( $perc * 2 );
	    			$html .= '<tr bgcolor="#eeeeee"><td>'.$p['Query_ID'].'</td><td>'.$p['Duration'].'</td><td><div style="float:left;width:50px;">'.$perc.'%</div> <div style="display:inline; margin-top:3px;float:left;background:#ff0000;width:'.$width.'px;height:10px;"></td><td>'.$p['Query'].'</td></tr>';
			}
			return $html .= '</table>';	
		}
	}
	
}

?>
