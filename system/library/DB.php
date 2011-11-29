<?php

/**
 *  RainFramework
 *  -------------
 *	Realized by Federico Ulfo & maintained by the Rain Team
 *	Distributed under MIT license http://www.opensource.org/licenses/mit-license.php
 */


/**
 * Class for DB database management (with PDO)
 */

class DB{

		static protected $db,								// database configurations
						 $fetch_mode = PDO::FETCH_ASSOC,	// define the type of results
						 $statement,						// the PDO object variable
						 $nquery = 0,
						 $link;

		/**
		 * Execute a query
		 *
		 * @param string $query
		 * @param array $field if you use PDO prepared query here you going to write the field
		 */
		static function query( $query=null,$field=array() ){
			try{
				self::$statement = self::$link->prepare($query);
				self::$statement->execute($field);
				self::$nquery++;
				return self::$statement;
			} catch ( PDOException $e ){
					error_reporting( "Error!: " . $e->getMessage() . "<br/>", E_USER_ERROR );
			}
		}



		/**
		 * Get the number of rows involved in the last query
		 *
		 * @param string $query
		 * @param array $field
		 * @return string
		 */
		static function count($query=null,$field=array()){
			return $query ? self::query($query,$field)->rowCount() : self::$statement->rowCount();
		}



		/**
		 * Get one field
		 *
		 * @param string $query
		 * @param array $field
		 * @return string
		 */
		static function get_field($query=null,$field=array()){
			return self::query($query,$field)->fetchColumn(0);
		}


		/**
		 * Get one row
		 *
		 * @param string $query
		 * @param array $field
		 * @return array
		 */
		static function get_row($query=null,$field=array() ){
			return self::query($query,$field)->fetch(self::$fetch_mode );
		}



		/**
		 * Get a list of rows. Example:
		 *
		 * $db->get_list("SELECT * FROM user")  => array(array('id'=>23,'name'=>'tim'),array('id'=>43,'name'=>'max') ... )
		 * $db->get_list("SELECT * FROM user","id")  => array(23=>array('id'=>23,'name'=>'tim'),42=>array('id'=>43,'name'=>'max') ... )
		 * $db->get_list("SELECT * FROM user","id","name")  => array(23=>'tim'),42=>'max' ...)
		 *
		 * @param string $query
		 * @param string $key
		 * @param string $value
		 * @param array $field
		 * @return array of array
		 */
		static function get_all( $query = null, $field=array(), $key = null, $value = null ){
			$rows = array();
			if( $result = self::query($query,$field)->fetchALL(self::$fetch_mode) ){
				if( !$key )
						return $result;
				elseif( !$value )
						foreach( $result as $row )
								$rows[ $row[$key] ] = $row;
				else
						foreach( $result as $row )
								$rows[ $row[$key] ] = $row[$value];
			}
			return $rows;
		}



		/**
		 * Get the last inserted id of an insert query
		 */
		static function get_last_id( ){
			return self::$link->lastInsertId();
		}



		/**
		 * Set the fetch mode
		 * PDO::FETCH_ASSOC for arrays, PDO::FETCH_OBJ for objects
		 */
		static function set_fetch_mode( $fetch_mode = PDO::FETCH_ASSOC ){
			self::$fetch_mode = $fetch_mode;
		}



		/**
		 * Insert Into
		 * @param array data The parameter must be an associative array (name=>value)
		 */
		static function insert( $table, $data ){
			if( count( $data ) ){
				$fields = $values = "";
					foreach( $data as $name => $value ){
						$fields .= $fields ? ",`$name`" : "`$name`";
						$values .= $values ? ",`$value`" : "`$value`";
					}
				return self::$link->query( "INSERT INTO $table ($fields) VALUES ($values)" );
			}
		}



		/**
		 * Update
		 * @param string $table the selected table
			 * @param array $data the parameter must be an associative array (name=>value)
		 */
		static function update( $table, $data, $where, $field = null ){
			if( count( $data ) ){
				$fields = "";
				foreach( $data as $name => $value )
					$fields .= $fields ? ",`$name`='$value'" : "`$name`='$value'";
				$where = is_string( $where ) ? " WHERE $where" : null;
							$query = "UPDATE $table SET $fields $where";
							return self::query( $query, $field );
			}
		}



		/**
		 * Delete
		 * @param array data The parameter must be an associative array (name=>value)
		 * @param string $where the condition of the row to be deleted
		 */
		static function delete( $table, $where ){
			return self::$link->query("DELETE FROM $table WHERE $where");
		}



		/**
		 * Begin a transaction
		 */
		static function begin(){
			return self::$link->beginTransaction();
		}



		/**
		 * Commit a transaction
		 */
		static function commit(){
			return self::$link->commit();
		}



		/**
		 * Rollback a transaction
		 */
		static function rollback(){
			return self::$link->rollBack();
		}



		/**
		 * Return the number of executed query
		 */
		static function get_executed_query( ){
			return self::$nquery;
		}



		/**
		 * Return > 0 if connected
		 */
		static function is_connected( ){
			return count(self::$link);
		}



		/**
		 * Connect to the database
		 */
		static function setup( $string, $username, $password ){
			try{
				self::$link = new PDO( $string, $username, $password );
				self::$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			} catch (PDOException $e) {
				die( "Error!: " . $e->getMessage() . "<br/>" );
			}

		}


		/**
		 * Configure the settings
		 *
		 */
		static function configure( $setting, $value ){
			if( is_array( $setting ) )
				foreach( $setting as $key => $value )
					$this->configure( $key, $value );
			else if( property_exists( __CLASS__, $setting ) )
				self::$$setting = $value;
		}


		/**
		 * Close mysql connection
		 */
		static function disconnect( ){
			unset( self::$link );
		}


}

// -- end