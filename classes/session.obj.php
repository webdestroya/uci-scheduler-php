<?php

// Maybe store sessions ENTIRELY in the memcache?
class Session
{
	private $sessionid			= "";
	private $username			= "guest";
	
	private $last_act			= 0;
	
	public function __construct($sessionid)
	{
		global $DB;
		
		$sessionid = strlen($sessionid)==0 ? md5("YOH MOMMA!".time()) : $sessionid;

		$DB->query("SELECT username FROM sessions WHERE sessionid='".$sessionid."'");
		if($DB->get_num_rows())
		{
			$res = $DB->fetch_row();
			$this->sessionid = $sessionid;
			$this->last_act = date(MYSQL_DATETIME);
			$this->username = $res['username'];
		}
		else 
		{
			$this->sessionid = $sessionid;
			$this->last_act = date(MYSQL_DATETIME);
			$this->username = "guest";
		}
	}
	
	public function setUser($uid)
	{
		$this->username = $uid;
	}
	
	public function isLoggedIn()
	{
		return $this->username=="guest" ? false : true;
	}
	
	public function getUser()
	{
		if($this->username!="guest")
		{
			return new User($this->username);
		}
		else 
		{
			return false;
		}
	}
	
	public function create()
	{
		global $DB;

		$DB->query("REPLACE INTO sessions (`sessionid`,`last_act`,`username`) VALUES ('".$this->sessionid."','".date(MYSQL_DATETIME)."','".$this->username."')");
	}
	
	public function update()
	{
		$this->create();
	}
	
	public function delete()
	{
		global $DB;
		$DB->query("DELETE FROM sessions WHERE sessionid='".$this->sessionid."'");
		$_SESSION = array();
	}	
}

?>
