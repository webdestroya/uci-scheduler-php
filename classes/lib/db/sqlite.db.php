<?php

class DB_SQLite extends Database 
{
	public function __construct()
	{
		
	}
	public function query($query){}
	
	public function connect(){}
	
	public function disconnect(){}
	
	public function escape($str){}
	
	public function get_num_rows(){}
	public function fetch_assoc(){}
	public function fetch_row(){}
	
	public function dbinsert($tbl,$params){}
}

?>