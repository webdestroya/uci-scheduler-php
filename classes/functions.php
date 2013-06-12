<?php


function getSections($data)
{
//$ctypes = array(" ","Lec","Sem","Dis","Lab","Fld","Act","Qiz","Col","Res","Stu");
	$sections = array();
	
	$lines = explode("\n",$data);
	$matchCount = 0;
	for($i=0;$i<count($lines);$i++)
	{
		$line = $lines[$i];
		
		preg_match_all(  "/(?P<ccode>[0-9]{5})(?:[ ]*)(?P<type>[A-Za-z]{3}) (?:[ ]*)"
						."(?P<sect>[A-Z0-9]{0,3})(?:[ ]*) (?P<units>[0-9])(?:[ ]*)(?P<teacher>.*) (?P<days>[MWFTShua]+)"
						."(?:[ ]*)(?P<start>[0-9p:]+)-(?: |)(?P<end>[0-9:p]+)(?:[ ]*)(?P<bldg>[A-Z]+)(?: |)"
						."(?P<room>[A-Z0-9]+)(?:.*)(?P<status>Waitl|FULL|OPEN|NewOnly)/",
						$line,
						$matches);
			
		if($matchCount>0 && $line=="")
		{
			break;
		}
		elseif($matches[0][0]!="")
		{
			if( !in_array($matches['type'][0], $sections) )
	        {
	            $sections[] = $matches['type'][0];
	        }
			$matchCount++;
		}
	}
    return $sections;
}

// translate letter days into 1-6
function getDays($days)
{
	$days = str_replace("M",	"1", $days);
	$days = str_replace("Tu",	"2", $days);
	$days = str_replace("W",	"3", $days);
	$days = str_replace("Th",	"4", $days);
	$days = str_replace("F",	"5", $days);
	$days = str_replace("Sa",	"6", $days);
	$days = str_replace("Su",	"7", $days);
	return $days;
}//getDays

// Splits the 12345 into an array
function daysArr($days)
{
	if(strlen($days)>1){return str_split($days);}
	else{return array($days);}
}//daysArr

// Gets the parent of the next class
function findParent($classes){return $classes[ count($classes)-1 ];}



function numOfSelectedCourse($data,$crs,$cnum)
{
	$count = 0;
	$lines = explode("\n",$data);
	foreach($lines as $line)
	{
		$line = strtoupper($line);
		if(substr_count($line,$crs) && substr_count($line,$cnum) )
		{
			preg_match_all(  "/".$crs."(.*)".$cnum."/",$line,$matches);
			if($matches[0][0])
			{
				$test = $matches[1][0]."T";
				$test = trim($test);
				if( strlen($test)==1)
				{
					$count++;
				}
			}
		}
	}
	return $count;
}

// Parses the course information that was gathered from WebSOC
function parseClasses($data,$par,$link=false,$crs="")
{
	global $GCLASSES, $RESTRICT, $DB;
	$GPARENT = $par;
	if($link){$GLINKS = $link;}
	else{$GLINKS = array();}
	
	$crsparts = explode("_",$crs);
	$numclasses = numOfSelectedCourse($data,str_replace("+"," ",$crsparts[0]),$crsparts[1]);
	
	$crsnice = str_replace(array("+","_")," ",$crs);
	$classes = array();
	
	
	$lines = explode("\n",$data);
	$matchCount = 0;
	for($i=0;$i<count($lines);$i++)
	{
		$line = $lines[$i];
		
		preg_match_all(  "/(?P<ccode>[0-9]{5})(?:[ ]*)"
						."(?P<type>[A-Za-z]{3}) (?:[ ]*)"
						."(?P<sect>[A-Z0-9]{1,3})(?:[ ]*) "
						."(?P<units>[0-9])(?:[ ]*)"
						."(?P<teacher>.*) "
						."(?P<days>[MWFTShua]+)(?:[ ]*)"
						."(?P<start>[0-9p:]{4,6})-(?: |)"
						."(?P<end>[0-9:p]{4,6})(?:[ ]*)"
						."(?P<bldg>[A-Z0-9]+)(?: |)"
						."(?P<room>[A-Z 0-9]+) (?:[ ]*)"
						."(?P<max>[0-9]{1,3})(?:[ ]*) "
						."(?P<enr>[0-9\/]+)(?:[ ]*) "
						."(?P<waitlist>[na0-9\/]+)(?:[ ]*) "
						."(?P<req>[0-9]{1,4})(?:[ ]*) "
						."(?P<nor>[0-9]{1,4})(?:[ ]*) "
						."(?P<rstr>[A-Za-z&]{0,5})(?:[ ]*) "
						."(?P<ead>Ead|)(?:[ ]*) "
						."(?P<status>Waitl|FULL|OPEN|NewOnly)/",
						$line,
						$matches);
		
		if($matchCount>$numclasses && $line=="")
		{
			break;
		}
		elseif($matches[0][0]!="")
		{
			//////////////////////////////////////////////
			$SECT = $matches['ccode'][0];
			$UNITS = $matches['units'][0];
			$CSECT = $matches['sect'][0];
			$TEACHER = $matches['teacher'][0];
			$DAYS = getDays($matches['days'][0]);
			$TYPE = $matches['type'][0];
			$STATUS = $matches['status'][0];
			if( substr($matches['end'][0], -1, 1)=="p")
			{
				$matches[5][$k] = trim($matches['end'][0],"p");
				$parte = explode(":",$matches['end'][0]);
				$parts = explode(":",$matches['start'][0]);
				if($parte[0]!="12")
				{
					$END = mktime($parte[0]+12,$parte[1],0,0,0,0);
					if($parts[0]!="12" && $parts[0]!="11")
					{
						$START = mktime($parts[0]+12,$parts[1],0,0,0,0);
					}
					else
					{
						$START = mktime($parts[0],$parts[1],0,0,0,0);
					}
				}
				else
				{
					$END = mktime($parte[0],$parte[1],0,0,0,0);
					$START = mktime($parts[0],$parts[1],0,0,0,0);
				}
			}
			else
			{
				$parte = explode(":",$matches['end'][0]);
				$parts = explode(":",$matches['start'][0]);
				$END = mktime($parte[0],$parte[1],0,0,0,0);
				$START = mktime($parts[0],$parts[1],0,0,0,0);
			}
			
			$BLDG = $matches['bldg'][0];
			$ROOM = $matches['room'][0];
			
			$restrictpass = true;
			
			if( $START < $RESTRICT['start'] )
			{
				$restrictpass = false;
			}
			if( $END > $RESTRICT['end'] )
			{
				$restrictpass = false;
			}
			foreach(daysArr($DAYS) as $day)
			{
				if(!in_array($day,$RESTRICT['days']) )
				{
					$restrictpass = false;
				}
			}
			
			if(in_array($STATUS,$RESTRICT['status']) && $restrictpass )
			{
				if(!in_array($TYPE,array("Tap","Tut")))
				{
					if(in_array($TYPE,$GLINKS))
					{
						$PARENT = findParent($classes[$GPARENT]);
						$GCLASSES[$PARENT]['link'][$TYPE][] = $SECT;
					}
				
					$classes[ $TYPE ][] = $SECT;
					
					$GCLASSES[$SECT] = array(
					'sect'=>$SECT,
					'days'=>daysArr($DAYS),
					'start'=>$START,
					'end'=>$END,
					'loc'=>$BLDG." ".$ROOM,
					'bldg'=>$BLDG,
					'room'=>$ROOM,
					'type'=>$TYPE,
					'status'=>$STATUS,
					'course'=>$crs,
					'crsnice'=>$crsnice,
					'units'=>$UNITS,
					'csect'=>$CSECT,
					'teacher'=>$TEACHER,
					'link'=>array(),
					);
					
					// Insert it into the database for later
					$DB->dbinsert("classes",array(
					'sect'=>$SECT,
					'days'=>implode(",",daysArr($DAYS)),
					'start'=>$START,
					'end'=>$END,
					'bldg'=>$BLDG,
					'room'=>$ROOM,
					'type'=>$TYPE,
					'status'=>$STATUS,
					));
				}//if its not tap/tut
			}//restrict status
			
			//////////////////////////////////////////////
			$matchCount++;
		}
	}
	return $classes;
}//parseClasses





// builds the schedule array
function buildsched($crs)
{
	global $GCLASSES, $CRSLIST, $COURSE;

    $data = array();

    if(count($crs)>0)
    {
        // pop the class off the front of the array
        $next = array_shift($crs);
        if( $next )
        {
            if(is_array($COURSE[ $next ][ $CRSLIST[ $next ][0] ]))
            {
                foreach($COURSE[ $next ][ $CRSLIST[ $next ][0] ] as $sect)
                {
                    if( count($GCLASSES[ $sect ]['link'])>0)
                    {
                        $linked = buildLinked($next,$sect);
                        foreach($linked as $lsect)
                        {
                        	$data[$lsect] = buildsched($crs);
                        }
                    }
                    else
                    {
                        $data[$sect]=buildsched($crs);
                    }
                }//course
            }
        }
    }
    return $data;
}//buildsched



function buildLinked2($crs, $type,$crsdata)
{
	global $GCLASSES, $CRSLIST, $COURSE;
	
	$data = array();
	
	if(count($type)>0)
	{
		// pop the class off the front of the array
		$next = array_shift($type);
		if( $next )
		{
			if(is_array($crsdata[ $next ]))
			{
				foreach($crsdata[ $next ] as $sect)
				{
					$data[$sect]=buildLinked2($crs, $type, $crsdata);
				}//course
			}
		}
	}
	return $data;
}//buildsched
function linkedarray2csv($arr)
{
	$data = array_keys($arr);
	$ret = $data[0];
	if( is_array($arr[$data[0]]) )
	{
		$ret .= ",".linkedarray2csv($arr[$data[0]]);
	}
	return $ret;
}
function buildLinked($crs,$sect)
{
	global $GCLASSES, $CRSLIST, $COURSE;
	
	$crstemp = $COURSE[$crs];
	
	
	$order = array();
	foreach($crstemp as $k=>$v)
	{
		$order[$k] = count($v);
	}
	arsort($order);
	$crss = array();
	foreach($order as $k=>$v)
	{
		$crss[] = $k;
	}//order
	
	$arrsize = 1;
	foreach($crstemp as $k=>$v)
	{
		$arrsize = $arrsize * count($COURSE[$crs][$k]);
	}
	$return = array();
	
	$ret2 = buildLinked2(
								$crs, 
								array_merge($CRSLIST[$crs][1],(array)$CRSLIST[$crs][0]), 
								array_merge(array('Lec'=>array($sect)),$GCLASSES[$sect]['link'])
								);
	//print_r($ret2);
	foreach($ret2 as $k=>$v)
	{
		$return[] = trim($k.",".linkedarray2csv( $v),",");
	}
	//print_r($return);
	return $return;
}
 


// checks to make sure the schedule is not impossible
// works by taking each section, and checking its time against all the other
// sections in the list.  if any conflict, it returns false.
function isPossible($arr)
{
	global $GCLASSES;
	
	$ret = true;
	foreach($arr as $sect)
	{
		foreach($arr as $sect2)
		{
			if($sect2!=$sect)
			{
				foreach($GCLASSES[$sect]['days'] as $day)
				{
					if( in_array($day,$GCLASSES[$sect2]['days']) )
					{
						if(	  false
						   || ( ($GCLASSES[$sect2]['start']>=$GCLASSES[$sect]['start']) 
						   		&& ($GCLASSES[$sect2]['end']<=$GCLASSES[$sect]['end']))
						   || ( ($GCLASSES[$sect2]['start']>=$GCLASSES[$sect]['start']) 
						   		&& ($GCLASSES[$sect2]['start']<=$GCLASSES[$sect]['end']))
						  )
						{
							$ret = false;
						}//if
					}//days
				}//foreach
			}//sect1=sect2
		}//arr
	}//arrsect
	return $ret;
}//isPossible


// Builds all schedule combinations
function buildPoss($arr,$line)
{
    global $FINALS, $CRSLIST, $POSSIBLES;

    foreach($arr as $k=>$v)
    {
    	if(is_array($v) && count($v)>0)
    	{
    		buildPoss($v,$line.$k.",");
    	}
    	else 
    	{
    		$POSSIBLES[] = $line.$k;
    	}
    }
}//buildPoss

// Distance between two buildings
function dist($b1,$b2)
{
	global $BUILDINGS;
	
	$x1 = $BUILDINGS[$b1]['x'];
	$x2 = $BUILDINGS[$b2]['x'];
	
	$y1 = $BUILDINGS[$b1]['y'];
	$y2 = $BUILDINGS[$b2]['y'];
	
	return round( sqrt(  pow(($x2-$x1),2) + pow(($y2-$y1),2)  ),2);
}//dist


function downtime($s1,$s2)
{
	global $GCLASSES;
	
	return $GCLASSES[$s2]['start'] - $GCLASSES[$s1]['end'];	
}

function needtorun($sects)
{
	global $DB, $GCLASSES;
	
	$needtorun = false;
	
	// get the buildings for the classes, and order them by time, so we know what buildings we are going to/from
	$DB->query("SELECT sect,days,bldg FROM classes WHERE sect IN (".implode(",",$sects).") ORDER BY start ASC");
	$DAYS = array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array());
	foreach($DB->fetch_assoc2() as $res)
	{
		$daylist = explode(",",$res['days']);
		foreach($daylist as $day)
		{
			$DAYS[$day][] = $res['sect'];//$res['bldg'];
		}
	}
	
	// now build the distances
	foreach($DAYS as $day=>$classes)
	{
		// now calculate the distances for each class
		for($i=1;$i<count($classes);$i++)
		{
			$dist = dist( $GCLASSES[$classes[$i-1]]['bldg'], $GCLASSES[$classes[$i]]['bldg']);
			
			if($dist>2 && downtime($classes[$i-1], $classes[$i])<700)
			{
				$needtorun = true;
			}
		}
	}
	
	return $needtorun;
}


function statusToColor($status,$sect=false)
{
	$inner = $sect ? $sect : $status;
	switch($status)
	{
		case "Waitl":
				$line = '<span class="red"><b>'.$inner.'</b></span>';
				break;
		case "FULL":
				$line = '<b>'.$inner.'</b>';
				break;
		case "OPEN":
				$line = '<span class="green"><b>'.$inner.'</b></span>';
				break;
		case "NewOnly":
				$line = '<span class="blue"><b>'.$inner.'</b></span>';
				break;
	}
	return $line;
}


// Calculate the total walking distance for that schedule
function schedDists($sects)
{
	global $DB;
	
	// get the buildings for the classes, and order them by time, so we know what buildings we are going to/from
	$DB->query("SELECT days,bldg FROM classes WHERE sect IN (".implode(",",$sects).") ORDER BY start ASC");
	$DAYS = array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array());
	foreach($DB->fetch_assoc2() as $res)
	{
		$daylist = explode(",",$res['days']);
		foreach($daylist as $day)
		{
			$DAYS[$day][] = $res['bldg'];
		}
	}
	
	// now build the distances
	$total = 0;
	foreach($DAYS as $day=>$classes)
	{
		
		// We need to set HOME as the start and end points for each day
		$classlist = array();
		$classlist[] = 'HOME';
		foreach($classes as $class)
		{
			$classlist[] = $class;
		}
		$classlist[] = 'HOME';
		
		// now calculate the distances for each class
		for($i=1;$i<count($classlist);$i++)
		{
			$total += dist($classlist[$i-1],$classlist[$i]);
		}
	}
	
	return $total;
}//schedDists


// Calculate the total downtime for each schedule
function schedDowntime($sects)
{
	global $DB;
	
	// get the buildings for the classes, and order them by time, so we know what buildings we are going to/from
	$DB->query("SELECT start,days,end FROM classes WHERE sect IN (".implode(",",$sects).") ORDER BY start ASC");
	$DAYS = array(1=>array(),2=>array(),3=>array(),4=>array(),5=>array(),6=>array());
	foreach($DB->fetch_assoc2() as $res)
	{
		$daylist = explode(",",$res['days']);
		foreach($daylist as $day)
		{
			$DAYS[$day][] = array($res['start'],$res['end']);
		}
	}
	
	// now build the distances
	$total = 0;
	$biggest = 0;
	$smallest = time();
	foreach($DAYS as $day=>$classes)
	{
		// now calculate the distances for each class
		for($i=1;$i<count($classes);$i++)
		{
			$temp = $classes[$i][0] - $classes[$i-1][1];
			
			$total += $temp;
			
			if($temp>$biggest)
			{
				$biggest = $temp;
			}
			if($temp<$smallest)
			{
				$smallest = $temp;
			}
		}
	}
	
	return array($total,$biggest,$smallest);
}//schedDowntime

function secToTime($sec)
{
    $data = "";
	if($sec>86400)
	{
		$data = (date("j",gmmktime(0,0,$sec))-11)."d ".ltrim(date("H",gmmktime(0,0,$sec)),"0")."h ".date("i",gmmktime(0,0,$sec))."m";
	}
	if($sec<60)
	{
		$data = "None";
	}
	if($sec<3600)
	{
		$data = ltrim(date("i",gmmktime(0,0,$sec)),"0")."m";
	}
	else 
	{
		$data = ltrim(date("H",gmmktime(0,0,$sec)),"0")."h ".date("i",gmmktime(0,0,$sec))."m";
	}
    $data = trim($data,"-");
    return $data;
}

?>
