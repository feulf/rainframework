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

        private $db,                // database configurations
                $fetch_mode = null;

        private $query_components = array();  // all the components to create a query, es $db->table('news')->limit(1); will set query_components[table]='news'

        private static  $obj_instance_list,
                        $nquery = 0;

        // ---- CONFIGURATION ----
        private static  $default_link_name = 'default',
                        $config_dir = CONFIG_DIR,
                        $config_file = 'db.php',
                        $default_fetch_mode = PDO::FETCH_ASSOC;
        // -----------------------



	/**
	 * Initialize the database link
	 *
	 * @param string $link_name  Set the link_name to use different database link connection
	 * @return MySql
	 */
        static function get_instance($link_name=null){

                if( !$link_name ) $link_name = self::$default_link_name;
                if( !isset(self::$obj_instance_list[$link_name] ) ){
                    self::$obj_instance_list[$link_name] = new self;
                    self::$obj_instance_list[$link_name]->_connect($link_name);
                }
                self::$obj_instance_list[$link_name]->fetch_mode = self::$default_fetch_mode;
                return self::$obj_instance_list[$link_name];
	}


        /**
         * Execute a query
         *
         * @param string $query
         * @param array $field if you use PDO prepared query here you going to write the field
         */
        function query( $query=null,$field=array() ){
            try{
                $statement = $this->link->prepare($query);
                $statement->execute($field);
                self::$nquery++;
                return $statement;
            } catch ( PDOException $e ){
                    error_reporting( "Error!: " . $e->getMessage() . "<br/>", E_USER_ERROR );
            }
        }


        /**
         * Get one field
         *
         * @param string $query
         * @param array $field
         * @return string
         */
        function get_field($query=null,$field=array()){
            return $this->query($query,$field)->fetchColumn(0);
        }


        /**
         * Get one row
         *
         * @param string $query
         * @param array $field
         * @return array
         */
        function get_row($query=null,$field=array() ){
            return $this->query($query,$field)->fetch($this->fetch_mode);
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
	function get_list( $query = null, $field = array(), $key = null, $value = null ){
            if( $result = $this->query($query,$field)->fetchALL($this->fetch_mode) ){
                if( !$key )
                        return $result;
                elseif( !$value )
                        foreach( $result as $row )
                                $rows[ $row[$key] ] = $row;
                else
                        foreach( $result as $row )
                                $rows[ $row[$key] ] = $row[$value];

                return isset($rows)?$rows:null;
            }
	}



	/**
	 * Get the last inserted id of an insert query
	 */
	function get_insert_id( ){
		return $this->link->lastInsertId();
	}



        /**
         * Set the fetch mode
         * PDO::FETCH_ASSOC for arrays, PDO::FETCH_CLASS for objects
         */
        function set_fetch_mode( $fetch_mode = PDO::FETCH_ASSOC ){
            $this->fetch_mode = $fetch_mode;
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
			return $this->link->query( "INSERT INTO $table ($fields) VALUES ($values)" );
		}
	}



	/**
	 * Update
	 * @param string $table the selected table
         * @param array $data the parameter must be an associative array (name=>value)
	 */
	function update( $table, $data, $where ){
		if( count( $data ) ){
			$fields = "";
			foreach( $data as $name => $value )
				$fields .= $fields ? ",`$name`='$value'" : ",`$name`='$value'";
			$where = is_string( $where ) ? " WHERE $where" : null;
			return $this->link->query("UPDATE $table SET $fields $where");
		}
	}



	/**
	 * Delete
         *
	 * @param array data The parameter must be an associative array (name=>value)
         * @param string $where the condition of the row to be deleted
	 */
	function delete( $table, $where ){
		return $this->link->query("DELETE $table WHERE $where");
	}



        /**
         * Begin a transaction
         */
        function begin_transaction(){
                return $this->link->beginTransaction();
        }



        /**
         * Commit a transaction
         */
        function commit_transaction(){
                return $this->link->commit();
        }



        /**
         * Rollback a transaction
         */
        function rollback_transaction(){
                return $this->link->rollBack();
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
		return count(self::$obj_instance_list);
	}



	/**
	 * Connect to the database
	 */
	private function _connect($link_name){

                if( !$this->db ){
                    require_once self::$config_dir . self::$config_file;
                    $this->db = $db;
                }

                extract( $this->db[$link_name] ); // get $dbserver, $hostname, $database, $username, $password, $database_path

		try{

			switch( $dbserver ){
				case 'mysql':
				case 'pgsql':
                                    $this->link = new PDO( "$dbserver:host=$hostname;dbname=$database", $username, $password );
                                    $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                                break;
				case 'sqlite':
					$this->link = new PDO( "sqlite:$database_path" );
                                        $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				break;
				case 'oracle':
					$this->link = new PDO( "OCI:", $username, $password );
                                        $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				break;
				case 'odbc':
					$this->link = new PDO( "odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq={$database_path};Uid={$username}");
                                        $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
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
		unset( $this->link );
	}


        private function __construct(){}

}

// -- end