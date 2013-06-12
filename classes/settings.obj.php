<?php

class Settings
{
	private $isvalid = false;


	private $searchid	= 0;
	private $term		= "";

	private $starttime	= "";
	private $endtime	= "";

	private $days		= array();

	private $types		= array();

	private $showdist	= false;

	public function import($arr)
	{
		foreach($arr as $k=>$v)
		{
			$this->$k = $v;
		}
	}
	public function export()
	{
		$arr = array();
		foreach($this as $k=>$v)
		{
			$arr[ $k ] = $v;
		}
		return $arr;
	}

	public function __construct($id=false)
	{
		global $DB;
		$showDefault = false;
		if($id)
		{
			$DB->query("SELECT searchid FROM users WHERE searchid='".$id."'");
			if($DB->get_num_rows())
			{
				$this->searchid = $DB->fetch_row("searchid");
			}
			else
			{
				$showDefault = true;
			}
		}
		else
		{
			$showDefault = true;
		}

		if($showDefault)
		{

			$DB->query("SELECT term FROM terms WHERE iscurrent='1'");
			$term = $DB->fetch_row("term");

			$DB->dbinsert("users",array(
			'date'=>date(MYSQL_DATETIME),
			'term'=>$term,
			'days'=>"M,Tu,W,Th,F",
			'starttime'=>"07:00:00",
			'endtime'=>"23:00:00",
			'status'=>"OPEN,Waitl",
			));

			$this->searchid = $DB->get_insert_id();

		}//


		// Now we select all the data from the DB.

		$DB->query("SELECT * FROM users WHERE searchid='".$this->searchid."'");
		if($DB->get_num_rows())
		{
			$res = $DB->fetch_row();
			
			$this->term = $res['term'];
			$this->days = explode(",",$res['days']);
			$this->types = explode(",",$res['status']);
			$this->starttime = $res['starttime'];
			$this->endtime = $res['endtime'];


		}
		else
		{
			throw new Exception("Cannot locate search settings");
		}

	}//construct

	public function reset()
	{
		global $DB;
		$DB->query("DELETE FROM user_sections WHERE searchid='".$this->searchid."'");
		$DB->query("DELETE FROM user_courses WHERE searchid='".$this->searchid."'");
	}

	public function save()
	{
		global $MEM;

		// update the memcache record
		//$arr = $this->export();
		//$MEM->set("settings_".$this->searchid, $arr);
		$MEM->set("schedlist_".$this->searchid, false);

	}

	public function __destruct()
	{
		//$this->save();
	}

	public function getID()
	{
		return $this->searchid;
	}

	public function getTerm()
	{
		return $this->term;
	}

	public function getStartTime()
	{
		return $this->starttime;
	}

	public function getEndTime()
	{
		return $this->endtime;
	}

	public function getDays()
	{
		return $this->days;
	}

	public function getTypes()
	{
		return $this->types;
	}

	public function getCourses()
	{
		global $DB;
		$DB->query("SELECT dept,cnum FROM user_courses WHERE searchid='".$this->searchid."'");
		if($DB->get_num_rows())
		{
			$rows = $DB->fetch_assoc();
			return $rows;
		}
		else
		{
			return array();
		}
	}

	public function getSections()
	{
		global $DB;
		$DB->query("SELECT ccode FROM user_sections WHERE searchid='".$this->searchid."'");
		if($DB->get_num_rows())
		{
			$ret = array();
			foreach($DB->fetch_assoc() as $res)
			{
				$ret[] = $res['ccode'];
			}
			return $ret;
		}
		else
		{
			return array();
		}
	}
	/////////////////////////////// SET

	public function addCcode($ccode)
	{
		global $DB;
		$DB->query("SELECT searchid FROM user_sections WHERE searchid='".$this->searchid."' AND ccode='".$ccode."'");
		if($DB->get_num_rows())
		{
			return false;
		}
		else
		{
			$DB->dbinsert("user_sections",array(
			'searchid'=>$this->searchid,
			'ccode'=>$ccode,
			));
			return true;
		}
	}

	public function delCcode($ccode)
	{
		global $DB;
		$DB->query("DELETE FROM user_sections WHERE searchid='".$this->searchid."' AND ccode='".$ccode."'");
	}

	public function setDays($days)
	{	
		global $DB;
		$DB->query("UPDATE users SET days='".implode(',',$days)."' WHERE searchid='".$this->searchid."'");
		$this->days = $days;
	}
	
	public function setTerm($term)
	{
		global $DB;
		$DB->query("UPDATE users SET term='".$term."' WHERE searchid='".$this->searchid."'");
		$this->term = $term;
		$this->reset();
	}
	
	public function setTypes($types)
	{
		global $DB;
		$DB->query("UPDATE users SET status='".implode(',',$types)."' WHERE searchid='".$this->searchid."'");
		$this->types = $types;
	}
	
	public function setSections($sects)
	{
		$this->sections = $sects;
	}

	public function setCourses($crs)
	{
		$this->courses = $crs;
	}
	
	public function delCourse($dept,$cnum=false)
	{
		global $DB;
		// SELECT ccode FROM user_sections WHERE searchid='8' AND ccode IN (SELECT ccode FROM crsnames WHERE dept='WRITING' AND crsnum='39C')
		if($cnum)
		{
			// check to make sure we dont have any ccodes in this course
			//$DB->query( "SELECT ccode FROM user_sections WHERE searchid='".$this->searchid."' AND ccode IN (SELECT ccode FROM crsnames WHERE dept='WRITING' AND crsnum='39C')

			// dept,cnum
			$DB->query("DELETE FROM user_courses WHERE searchid='".$this->searchid."' AND dept='".$dept."' AND cnum='".$cnum."'");
		}
		else
		{
			// expect a hash
			$DB->query("DELETE FROM user_courses WHERE searchid='".$this->searchid."' AND MD5( CONCAT(dept,cnum) )='".$dept."'");
		}
	}
	
	public function addCourse($dept,$cnum)
	{
		global $DB;

		$this->delCourse($dept,$cnum);
		$DB->dbinsert("user_courses",array(
		'searchid'=>$this->searchid,
		'dept'=>$dept,
		'cnum'=>$cnum,
		));
	}
	
	public function setStartTime($time)
	{
		global $DB;
		$DB->query("UPDATE users SET starttime='".$time."' WHERE searchid='".$this->searchid."'");
		$this->starttime = $time;
	}
	
	public function setEndTime($time)
	{
		global $DB;
		$DB->query("UPDATE users SET endtime='".$time."' WHERE searchid='".$this->searchid."'");
		$this->endtime = $time;
	}
}//settings


?>
