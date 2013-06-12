<?php

class Session
{
	private $sessionid		= "";
	private $userid			= 0;
	private $last_act		= "0000-00-00 00:00:00";
	private $ip				= "";
	
	public function __construct($sessid)
	{
		global $DB;
		
		$this->sessionid = $sessid;
		$this->last_act = FUNC::make_mysql_date();
		$this->ip 		= $_SERVER['REMOTE_ADDR'];
				
		$DB->query(  "SELECT userid,ip FROM `sessions` "
					."WHERE `sessionid`='".$this->sessionid."'" );
		if($DB->get_num_rows())
		{
			$res = $DB->fetch_row();
			$this->userid	 			= $res['userid'];
			$this->ip 					= $res['ip'];
		}
		else 
		{
			$this->create();
		}
	}
	
	public function getSessionID(){return $this->sessionid;}
	public function getUserID(){return $this->userid;}
	public function getLastAct(){return $this->last_act;}
	public function getIPAddress(){return $this->ip;}
	public function getIP(){return $this->getIPAddress();}

	public function setUserID($id){$this->userid = $id;}
	
	public function isLoggedIn()
	{
		return ($this->userid!=0) ? 1 : 0;
	}
	
	private function buildDBVars()
	{
		$update = array(
		'sessionid'=>$this->sessionid,
		'userid'=>$this->userid,
		'ip'=>$this->ip,
		'last_act'=>$this->last_act,
		);
		return $update;
	}
	
	public function delete()
	{
		global $DB;
		$DB->query( "DELETE FROM `sessions` WHERE `sessionid`='".$this->sessionid."'" );
	}
	
	public function create()
	{
		global $DB;
		$insert = $DB->dbinsert("sessions", $this->buildDBVars());
	}
	
	public function update()
	{
		global $DB;
		$update = $DB->compile_db_update_string($this->buildDBVars());
		$DB->query( "UPDATE `sessions` SET ".$update." WHERE `sessionid`='".$this->sessionid."'" );
	}
}

?>
