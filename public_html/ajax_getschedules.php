<?php

include "includes.php";

$SEARCHID = $_POST['searchid'];


function showError($no, $str)
{
	global $SEARCHID;
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");

	header("Content-Type: application/json");

	echo "{";
	echo "\"searchid\":\"".$SEARCHID."\",";
	echo "\"error\":\"".$no."\",";
	echo "\"errorstr\":\"".$str."\"";
	echo "}";
	die();

}



$USR = new Settings($SEARCHID);

if($USR->getID()!=$SEARCHID)
{
	showError("1","Your search has expired");
}

$data = $MEM->get("schedlist_".$SEARCHID);
if($data)
{
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");

	header("Content-Type: application/json");

	echo $data;
	die();
}

// Section start/end times
$SECTSE = array();


function getStartTime($sect)
{
	global $SECTSE;
	$var = explode(":",$SECTSE[$sect]['s']);
	return ( (3600*intval($var[0])) + (60*intval($var[1])) );
}

function getEndTime($sect)
{
	global $SECTSE;
	$var = explode(":",$SECTSE[$sect]['e']);
	return ( (3600*intval($var[0])) + (60*intval($var[1])) );
}

function getDays($sect)
{
	global $SECTSE;
	return explode(",", $SECTSE[$sect]['d']);
}



$crss = $USR->getCourses();
if( count($crss) > 0 )
{
	// get the course types
	$TYPES = array();
	$COURSES = array();

	// get the number of classes that should be in the schedule
	$numClasses = 0;
	foreach($crss as $crs)
	{
		$DB->query("SELECT DISTINCT type FROM courses WHERE "
			."term='".$USR->getTerm()."' "
			."AND ccode IN ("
				."SELECT ccode FROM crsnames WHERE term=courses.term "
				."AND dept='".$crs['dept']."' "
				."AND crsnum='".$crs['cnum']."' "
			.")"
			);
		if($DB->get_num_rows())
		{
			foreach($DB->fetch_assoc() as $res)
			{
				$TYPES[] = array(
					'dept'=>$crs['dept'],
					'cnum'=>$crs['cnum'],
					'type'=>$res['type'],
					);
			}
		}
		else
		{
			// wtf?
		}
	}
	$numClasses = count($TYPES);

	// POPULATE THE SECTSE TABLE
	foreach($crss as $crs)
	{
		$DB->query("SELECT ccode,days,starttime,endtime FROM courses WHERE term='".$USR->getTerm()."' AND ("
				."ccode IN (SELECT ccode FROM crsnames WHERE term=courses.term AND dept='".$crs['dept']."' AND crsnum='".$crs['cnum']."')"
			.")"
			);
			foreach($DB->fetch_assoc() as $res)
			{
				$SECTSE[ $res['ccode'] ] = array(
				's'=>$res['starttime'],
				'e'=>$res['endtime'],
				'd'=>$res['days'],
				);
			}

	}

	// get any section restrictions
	$sects = $USR->getSections();

	
	// make temp table
	$query = "CREATE TEMPORARY TABLE tmpcrs (";
	$query .= "id varchar(32) not null default '', ";
	for($i=0;$i<$numClasses;$i++)
	{
		$query .= "ccode".($i+1)." mediumint(10) unsigned not null default '0', ";
	}
	$query .= "PRIMARY KEY(id)";
	$query .= ")";
	$DB->query($query);

	$loop = 0;
	$CCODES = array();

	// Build up the table
	foreach($crss as $crs)
	{
		$CCODES[ $loop ] = array();
		$tmccodes = array();
		$DB->query("SELECT ccode FROM crslinks WHERE term='".$USR->getTerm()."' AND "
		."ccode IN (SELECT ccode FROM crsnames WHERE term=crslinks.term AND dept='".$crs['dept']."' AND crsnum='".$crs['cnum']."')");
		if($DB->get_num_rows())
		{
			// get the main course ccodes
			foreach($DB->fetch_assoc() as $res)
			{
				$tmccodes[] = $res['ccode'];
			}

			// get all the children of all types
			foreach($tmccodes as $tmc)
			{
				$tlccodes = array();
				$DB->query("SELECT childccode, (SELECT type FROM courses WHERE term=crslinks.term AND ccode=crslinks.childccode) AS type "
				."FROM crslinks WHERE ccode='".$tmc."' AND term='".$USR->getTerm()."'");
				if($DB->get_num_rows())
				{
					foreach($DB->fetch_assoc() as $res)
					{
						$tlccodes[ $res['type'] ][] = $res['childccode'];
					}
					
					// get the total course counts
					$ccounts = 1;
					foreach($tlccodes as $k=>$v)
					{
						$ccounts = $ccounts * count($v);
					}

					$tccs = array_fill(0,$ccounts,$tmc);

					// now we setup the course codelists
					foreach($tlccodes as $type=>$ccs)
					{
						$lpct = $ccounts/ count($ccs);
						foreach($ccs as $k=>$cc)
						{
							for($i=0;$i<$lpct;$i++)
							{
								$tccs[$i+($k*$lpct)] .= ",".$cc;
							}
						}
					}//tlc
					$CCODES[ $loop ] = array_merge($CCODES[$loop], $tccs);

				}//numrows
			}
		}
		else
		{
			$DB->query("SELECT ccode FROM crsnames WHERE term='".$USR->getTerm()."' AND dept='".$crs['dept']."' AND crsnum='".$crs['cnum']."'");
			if($DB->get_num_rows())
			{
				foreach($DB->fetch_assoc() as $res)
				{
					$CCODES[ $loop ][] = $res['ccode'];
				}
			}
			else
			{
				$_SESSION['error_msg'] = "Sorry, but there was a problem finding sections for <b>".$crs['dept']." ".$crs['cnum']."</b>.";
				redirect("/search/".$SEARCHID);
			}
			

		}

		$loop++;
	}//bld tbl
	

	// remove the duplicats
	$totCourses = 1;
	foreach($CCODES as $l=>$ccs)
	{
		$CCODES[ $l ] = array_unique($CCODES[$l]);

		$totCourses = $totCourses * count($CCODES[$l]);
	}
	
	
	// merge the ccode arrays
	$FINALCCODES = array();
	foreach($CCODES as $ccs)
	{
		$lpct = $totCourses / count($ccs);
		foreach($ccs as $k=>$cc)
		{
			for($i=0;$i<$lpct;$i++)
			{
				$FINALCCODES[ $i+($k*$lpct) ] .= ",".$cc;
			}
		}
	}

	
	// insert final ccodes into schedule table
	foreach($FINALCCODES as $cc)
	{
		$cc = trim($cc,",");
		$parts = explode(",",$cc);
		$qparts = array();
		$qparts['id'] = md5($cc);
		foreach($parts as $k=>$part)
		{
			$qparts[ 'ccode'.($k+1) ] = $part;
		}
		
		$DB->dbinsert("tmpcrs", $qparts,true);
	}

	
	// clear any bogus sets
	$query = "DELETE FROM tmpcrs WHERE 0 ";
	for($i=0;$i<$numClasses;$i++)
	{
		$query .= "OR ccode".($i+1)."='0' ";
	}
	$DB->query($query);
	

	// Do we need to apply any section restrictions?
	if( count($sects) > 0 )
	{
		$dquery = "DELETE FROM tmpcrs WHERE 1 ";
		foreach($sects as $ccode)
		{
			for($i=0;$i<$numClasses;$i++)
			{
				$dquery .= "AND ccode".($i+1)."!='".$ccode."' ";
			}
		}
		$DB->query($dquery);
	}


	// remove any start times
	$dquery = "DELETE FROM tmpcrs WHERE 0 ";
	for($i=0;$i<$numClasses;$i++)
	{
		$dquery .= "OR ccode".($i+1)." IN (SELECT ccode FROM courses WHERE term='".$USR->getTerm()."' AND starttime<'".$USR->getStartTime()."') ";
	}
	$DB->query($dquery);
	
	// remove any end times
	$dquery = "DELETE FROM tmpcrs WHERE 0 ";
	for($i=0;$i<$numClasses;$i++)
	{
		$dquery .= "OR ccode".($i+1)." IN (SELECT ccode FROM courses WHERE term='".$USR->getTerm()."' AND endtime>'".$USR->getEndTime()."') ";
	}
	$DB->query($dquery);

	// remove any status restrictions
	$dquery = "DELETE FROM tmpcrs WHERE 0 ";
	for($i=0;$i<$numClasses;$i++)
	{
		$dquery .= "OR ccode".($i+1)." NOT IN (SELECT ccode FROM ccode_status WHERE term='".$USR->getTerm()."' AND status IN ('".implode("','",$USR->getTypes())."')) ";
	}
	$DB->query($dquery);

	
	// remove any day restrictions
	$daylist = array('M','Tu','W','Th','F','Sa','Su');
	$rmdays = array_diff($daylist, $USR->getDays() );
	
	$dquery = "DELETE FROM tmpcrs WHERE 0 ";
	for($i=0;$i<$numClasses;$i++)
	{
		foreach($rmdays as $day)
		{
			$dquery .= "OR ccode".($i+1)." IN (SELECT ccode FROM courses WHERE term='".$USR->getTerm()."' AND FIND_IN_SET('".$day."',days)>0 ) ";
		}
	}
	$DB->query($dquery);

	$daylist = array('M','Tu','W','Th','F','Sa','Su');

	// TODO: check for time collisions
	$DB->query("SELECT * FROM tmpcrs");
	$rows = $DB->fetch_assoc();
	foreach($rows as $res)
	{
		$tmpdays = array();
		foreach($daylist as $tmday)
		{
			$tmpdays[$day] = array();
			$tmpdays[$tmday] = array_fill(0,145,"0");
		}
		


		$cclist = array();
		$id = 0;
		foreach($res as $k=>$v)
		{
			if($k=="id")
			{
				$id = $v;
			}
			else
			{
				$cclist[] = $v;
			}
		}
	
		$isInvalid = false;
	
		foreach($cclist as $cc)
		{
			if(!$isInvalid)
			{
				$ccdays = getDays($cc);
				$starttime = getStartTime($cc) / 600;
				$endtime = getEndTime($cc) / 600;

				foreach($ccdays as $day)
				{
					if(!$isInvalid)
					{
						for($i=$starttime; $i<=$endtime; $i++)
						{
							if( $tmpdays[ $day ][$i] == "1" )
							{
								$isInvalid = true;
								break;
							}
						}

						if(!$isInvalid)
						{
							for($i=$starttime; $i<=$endtime; $i++)
							{
								$tmpdays[ $day ][$i] = "1";
							}
						}
						else
						{
							$DB->query("DELETE FROM tmpcrs WHERE id='".$id."'");
						}
					}
					else
					{
						break;
					}
				}
			}
			else
			{
				break;
			}
		}//cc
	}//time collision


	$ccodeselect = array();
	for($i=0;$i<$numClasses;$i++)
	{
		$ccodeselect[] = "ccode".($i+1);
	}
	

	ob_start();

	echo "{";
	//hash,title,cnum,dept,error
	echo "\"error\":\"0\",";
	echo "\"searchid\":\"".$SEARCHID."\",";

	echo "\"schedules\": [";
		$lines = array();
		$DB->query("SELECT ".implode(",",$ccodeselect)." FROM tmpcrs");
		foreach($DB->fetch_assoc() as $res)
		{
			$sch = array();
			
			$sch['hash'] = implode("x", $res);
			$sch['ccodes'] = implode(",", $res);

			$sched = array();
			foreach($sch as $k=>$v)
			{
				$sched[] = $k.":\"".$v."\"";
			}

			$lines[] = "{".implode(",",$sched)."}";
		}
		echo implode(",",$lines);
	echo "]";

	echo "}";
	
	$data = ob_get_clean();

	$MEM->set("schedlist_".$SEARCHID, $data);
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");

	header("Content-Type: application/json");


	echo $data;

	die();





}
else
{
	showError("1","Your search has expired.");	
}


?>
