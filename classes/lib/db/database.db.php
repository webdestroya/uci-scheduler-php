<?php

abstract class Database
{
	protected $connid		= null;
	protected $queryid		= null;
	
	protected $dbname		= "";
	protected $user			= "";
	protected $password		= "";
	protected $hostname		= "localhost";
	
	protected $connected 	= false;
	
	protected $queries		= array();
	protected $recording	= false;
	
	///////////////////////////////////////////////////////////////////
	
	abstract public function __construct();
	
	public function startRecording()
	{
		$this->recording = true;
	}
	
	public function endRecording()
	{
		$this->recording = false;
		return $this->queries;
	}
	
	public function __destruct()
	{
		$this->disconnect();
	}
	
	final public function setDBName($db)
	{
		$this->dbname = $db;
	}
	final public function setUsername($uname)
	{
		$this->user = $uname;
	}
	final public function setPassword($pass)
	{
		$this->password = $pass;
	}
	final public function setHostname($host)
	{
		$this->hostname = $host;
	}
	
	abstract public function query($query);
	
	abstract public function connect();
	
	abstract public function disconnect();
	
	abstract public function escape($str);
	
	abstract public function get_num_rows();
	abstract public function fetch_assoc();
	abstract public function fetch_row();
	
	abstract public function dbinsert($tbl,$params);
	
	// public function dbreplace($tbl,$params);
	
}

class DBCouldNotConnectException extends Exception {}
class DBQueryErrorException extends Exception {}

?>