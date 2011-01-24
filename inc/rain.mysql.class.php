<?php

/**
 * Rain.MySql, manage MySql query as array
 *
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation;
 * either version 3 of the License, or any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 * 
 *  @author Federico Ulfo <rainelemental@gmail.com>
 *  @copyright 2006 - 2010 Federico Ulfo | www.federicoulfo.it
 *  @link http://www.rainframework.com
 *  @version 2.0
 *  @package RainFramework
 */



/**
 * Class for MySql database management
 */

class MySql{

	var $result, 					// result of the query
		$link,						// database link
		$link_name = 'default';	// name of the database link ( default: default_db )

	static $nquery = 0,				// count the query executed
		   $link_array = array(),	// link array
		   $exit_on_error = true;  	// if true, on error exit from the execution

	/**
	 * Initialize the database link
	 *
	 * @param string $link_name  Set the link_name to use different database link connection
	 * @return MySql
	 */
	function MySql(){
		$this->link = isset( mysql::$link_array[$this->link_name] ) ? mysql::$link_array[$this->link_name] : null;
	}

	/**
	 * Connect to the database
	 */
	function connect( $hostname = null, $username = null, $password = null, $database = null ){

		if( !$hostname && !$username && !$database )
			require CONF_DIR . "conf.db.php";

		if( $this->link = mysql::$link_array[$this->link_name] = mysql_connect( $db[$this->link_name]['hostname'], $db[$this->link_name]['username'], $db[$this->link_name]['password'] ) or die( mysql_error() ) )
	    	return mysql_select_db( $db[$this->link_name]['database'] ) or die ( mysql_error() );
	}



	/**
	 * Close mysql connection
	 */
	function disconnect( ){
		return mysql_close( $this->link );
	}
	
	
	/**
	 * Execute query. Use this function for update/delete query, for read query use getField, getRow, getArrayRow ...
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
	        	if( mysql::$exit_on_error )
		        	exit;
			}
		}
		else
			return $this->result[ $query ];
	}



	/**
	 * Return the number of rows of the query
	 * @return int
	 */
	function numRows( $query = null ){
		if( $result = $this->query( $query ) )
			return mysql_num_rows( $result );
	}
	
	
	/**
	 * Return the selected field. E.g.:
	 * $name = $db->getField( "name", "SELECT name FROM user LIMIT 1" );
	 * 
	 * @param string field Field you want to select in your query
	 * @return string/int
	 */
	function getField( $field, $query = null ){
		if( $row = $this->getRow( $query ) and isset( $row[$field] ) )
			return $row[$field];

	}
	
	
	
	/**
	 * Return the selected row as array. E.g.:
	 * $user = $db->getRow( "SELECT * FROM user LIMIT 1" ); 
	 * // return: array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' )
	 * 
	 * @return array
	 */
	
	function getRow( $query = null ){
		return mysql_fetch_array( $this->query( $query ), MYSQL_ASSOC );
	}



	/**
	 * Return the selected rows as array. E.g.:
	 * $user_list = $db->getArrayRow( "SELECT * FROM user LIMIT 5" );
	 * // return: $user_list => array( 0 => array( 'user_id'=>1, 'name'=>'Rain', 'status'=>'admin' ), ... , 4 => array( ... ) )
	 * 
	 * @return array
	 */

	function getArrayRow( $query = null, $key = null, $value = null ){
		if( $key && $value )
			while( $row = mysql_fetch_array( $this->query($query), MYSQL_ASSOC ) )
				$rows[ $row[$key] ] = $row[$value];
		
		elseif( $key )
			while( $row = mysql_fetch_array( $this->query($query), MYSQL_ASSOC ) )
				$rows[ $row[$key] ] = $row;
		
		else
			while( $row = mysql_fetch_array( $this->query( $query ), MYSQL_ASSOC ) )
				$rows[ ] = $row;
		
		return isset($rows)?$rows:null;
	}



	/**
	 * Return the last inserted id of an insert query
	 */
	
    function getInsertedId( ){
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