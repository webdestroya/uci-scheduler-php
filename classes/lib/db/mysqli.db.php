<?php

class DB_MySQLi extends Database 
{
	private $conn_id		= null;
	private $query_id		= null;
	
	private $debug = "";
	private $queryct = 0;
	private $totaltime = 0;
	
	public function getDebug()
	{
		$data = '<div align="center">';
		$data .= "Executed ".$this->queryct." queries in ".$this->totaltime;
		$data .= '<table border="1">';
		$data .= $this->debug;
		$data .= '</table>';
		$data .= "Executed ".$this->queryct." queries in ".$this->totaltime;
		$data .= '</div>';
		
		return $data;
	}
	
	public function __construct()
	{
		
	}
	
	public function __destruct()
    {   
        //debug("Disconnecting from the MySQL Server");
        $this->disconnect();
    }
    
    public function disconnect()
    {
    	if($this->connected)
    	{
    		$this->free_result();
        	mysqli_close($this->conn_id);
    	}
    }

    public function connect()
    {   
        $this->conn_id = mysqli_connect($this->hostname, $this->user, $this->password, $this->dbname);

        if(!$this->conn_id)
        {
            throw new DBQueryErrorException("An error occurred accessing the database. Please try again later.");
        }
        else 
        {
        	$this->connected = true;
        }
        return true;
    }//

	public function temptbl($orig)
	{
		$this->query("SHOW CREATE TABLE ".$orig);
		$data = $this->fetch_row("Create Table");
		$data = str_replace("CREATE TABLE `", "CREATE TEMPORARY TABLE `temp_",$data);
		$data = str_replace("\n", " ", $data);
		$data = preg_replace("/ENGINE=(.*)$/","",$data);
		$this->query($data);
	}




    public function escape($str)
    {
    	if(!$this->connected)
    	{
    		$this->connect();
    		$this->connected = true;
    	}
        return mysqli_real_escape_string($this->conn_id, $str);
    }

    public function free_result()
    {   
        @mysqli_free_result($this->query_id);
    }

	public function check_connection()
    {   
        if(!mysqli_ping($this->conn_id))
        {
            //debug("Lost MySQL connection, trying to reconnect");
            $this->connect();
            if(!mysqli_ping($this->conn_id))
            {
                //debug("Unable to reconnect to MySQL server");
                throw new DBCouldNotConnectException("An error occurred accessing the database. Please try again later.");
            }
        }
    }

    public function query($query)
    {
    	if(!$this->connected)
    	{
    		$this->connect();
    		$this->connected = true;
    	}
    	
    	if($this->recording)
    	{
    		$this->queries[] = $query;
    	}
    	else 
    	{
    		if($_GET['debug']=="1")
	        {
	        	if(substr($query,0,6)=="SELECT")
	        	{//10
	        		$this->debug .= "<tr><td colspan='10'>$query</td></tr>";
	        		$eid = mysqli_query($this->conn_id, "EXPLAIN ".$query);
	        		$this->queryct++;
	        		$this->debug .= "<tr>";
	        		$this->debug .= "<td>ID</td>";
	        		$this->debug .= "<td>Type</td>";
	        		$this->debug .= "<td>Table</td>";
	        		$this->debug .= "<td>Type</td>";
	        		$this->debug .= "<td>PossKeys</td>";
	        		$this->debug .= "<td>Key</td>";
	        		$this->debug .= "<td>KeyLen</td>";
	        		$this->debug .= "<td>Ref</td>";
	        		$this->debug .= "<td>Rows</td>";
	        		$this->debug .= "<td>Extra</td>";
	        		$this->debug .= "</tr>";
	        		$data = array();
			        while($res = mysqli_fetch_assoc( $eid ) )
			        {
			            $data[] = $res;
			        }
			        $this->free_result();
	        		foreach($data as $row)
	        		{
	        			$this->debug .= "<tr>";
	        			foreach($row as $k=>$v)
	        			{
	        				$this->debug .= "<td>".$v."</td>";
	        			}
	        			$this->debug .= "</tr>";
	        		}
	        	}
	        }
    		
	    	$start = microtime(true);
	        $this->query_id = mysqli_query($this->conn_id, $query);
			$end = microtime(true)-$start;
			$end = round($end,6);
	        if(!$this->query_id )
	        {
	            $test = "";
	            if(true)//defined('IS_TASK'))
	            {
	                $test = $query;
	            }
	
	            // throw an exception
	            throw new DBQueryErrorException("An error occurred accessing the database.<!--".base64_encode($test)."-->");
	        }
	        
	        if($_GET['debug']=="1")
	        {
	        	$this->totaltime = $this->totaltime + $end;
	        	$this->debug .= "<tr><td colspan='10'>Time: ".$end."</td></tr>";
	        }
	        
	        return $this->query_id;
    	}
    }//

    public function select_db($db)
    {
        return mysqli_select_db($this->conn_id, $db);
    }

    public function get_num_rows()
    {
        $rows = mysqli_num_rows( $this->query_id );
        return $rows==NULL ? false : $rows;
    }

    public function fetch_row($var=false)
    {
    	$res = mysqli_fetch_array( $this->query_id, MYSQLI_ASSOC );
       	$this->free_result();
    	if($var)
    	{
    		$res = $res[$var];	
    	}
        return $res;
    }

    public function fetch_assoc2()
    {
        return mysqli_fetch_assoc( $this->query_id );
    }//

    public function fetch_assoc()
    {
        $data = array();
        while($res = mysqli_fetch_assoc( $this->query_id ) )
        {
            $data[] = $res;
        }
        $this->free_result();
        return $data;
    }//

    private function fetch_fields()
    {
        return mysqli_fetch_fields($this->query_id);
    }

    private function fetch_lengths()
    {
        return mysqli_fetch_lengths($this->query_id);
    }

    private function field_count()
    {
        return mysqli_field_count($this->query_id);
    }

    public function get_insert_id()
    {
        return mysqli_insert_id($this->conn_id);
    }

    public function affected_rows()
    {
        return mysqli_affected_rows($this->conn_id);
    }

    private function error()
    {
        // 0 - error number | 1 = error string
        return array(mysqli_error($this->conn_id),mysqli_errno($this->conn_id));
    }

    /**
     * Create an array from a multidimensional array returning formatted
     * strings ready to use in an INSERT query, saves having to manually format
     * the (INSERT INTO table) ('field', 'field', 'field') VALUES ('val', 'val')
     * @param array $data array of values [field]=value
     * @return array
     */
    private function compile_db_insert_string($data)
    {
        $field_names  = "";
        $field_values = "";

        foreach ($data as $k => $v)
        {
            if( strlen($v) > 65500 )
            {
                $v = substr($v,0,65500);
            }
            $v = stripslashes($v);
            $v = $this->escape($v);
            //$v = preg_replace( "/'/", "\\'", $v );
            //$v = addslashes($v);
            //$v = preg_replace( "/#/", "\\#", $v );
            $field_names  .= "`$k`,";
            $field_values .= "'$v',";
        }

        $field_names  = preg_replace( "/,$/" , "" , $field_names  );
        $field_values = preg_replace( "/,$/" , "" , $field_values );

        return array( 'FIELD_NAMES'  => $field_names,
                      'FIELD_VALUES' => $field_values,
                    );
    }//compile_db_insert_string

    public function dbinsert($tbl,$vars,$ignore=false)
    {
        $insert = $this->compile_db_insert_string($vars);
		$igstr = $ignore ? " IGNORE" : "";
        $this->query("INSERT".$igstr." INTO ".$tbl." (".$insert['FIELD_NAMES'].") VALUES (".$insert['FIELD_VALUES'].")");
    }
    public function dbreplace($tbl,$vars)
    {
        $insert = $this->compile_db_insert_string($vars);
        $this->query("REPLACE INTO ".$tbl." (".$insert['FIELD_NAMES'].") VALUES (".$insert['FIELD_VALUES'].")");
    }

    /**
     * Create an array from a multidimensional array returning a formatted
     * string ready to use in an UPDATE query, saves having to manually format
     * the FIELD='val', FIELD='val', FIELD='val'
     * @param array $data array of values [field]=value
     * @return array
     */
    private function compile_db_update_string($data)
    {
        $return_string = "";
        foreach ($data as $k => $v)
        {
            if( strlen($v) > 65500 )
            {
                $v = substr($v,0,65500);
            }
            $v = preg_replace( "/'/", "\\'", $v );
            $return_string .= "`".$k."`='".$v."',";
        }
        $return_string = preg_replace( "/,$/" , "" , $return_string );
        return $return_string;
    }//compile_db_update_string

    // Aliases
    public function insert_string($data)
    {
        return $this->compile_db_insert_string($data);
    }

    public function update_string($data)
    {
        return $this->compile_db_update_string($data);
    }

}

?>
