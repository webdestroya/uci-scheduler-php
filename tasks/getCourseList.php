<?php

include "taskinc.php";

// translate letter days into 1-6
function getDays($days)
{
	$ret = array();
	$ldays = array("M","Tu","W","Th","F","Sa","Su");
	foreach($ldays as $ld)
	{
		if( substr_count($days, $ld) )
		{
			$ret[] = $ld;
		}
	}
	return $ret;
}//getDays

// GET CURRENT TERM
$DB->query("SELECT term FROM terms WHERE iscurrent='1'");
$TERM = $DB->fetch_row("term");

$DB->temptbl("ccode_status");
$DB->temptbl("courses");
$DB->temptbl("crslinks");
$DB->temptbl("crsnames");

// get dept list
$DB->query("SELECT dept FROM depts");
$depts = array();
foreach($DB->fetch_assoc() as $res)
{
	$depts[] = $res['dept'];
}
//$dept = "SOC SCI";
// go thru all depts
foreach($depts as $dept)
{
	// DOWNLOAD FROM WEBSOC
	$data = getCourses($dept, $TERM);
	//$data = getCourses($dept, $TERM,"1-198");
	//$data .= getCourses($dept, $TERM,"199-499");


	$lines = explode("\n", $data);

	$classes = array();
	$GCLASSES = array();
	$GLINKS = array();
	$GPARENT = "";

	//$dept = "SOC SCI";
	$cnum = "";
	$name = "";

	$hasSetParent = false;

	foreach($lines as $line)
	{
		$line = trim($line);
		$crs = preg_match("/^"
							.$dept
							."(?:[ ]+)"
							."(?P<CrsNum>[0-9A-Z]+)"
							."(?:[ ]+)"
							."(?P<Name>.+)"
							."$/i", $line, $match);
		
		if($crs)
		{
			$cnum = strtoupper(trim($match['CrsNum']));
			$name = strtoupper(trim($match['Name']));
			echo $dept." ".$cnum." -- ".$name."\n";
			$hasSetParent = false;

		}
		else
		{
			// its a section list, check that
			$found = preg_match( "/^"
								."(?P<ccode>[0-9]{5})(?:[ ]*)"
								."(?P<type>[A-Za-z]{3}) (?:[ ]*)"
								."(?P<sect>[A-Z0-9]{1,3})(?:[ ]*) "
								."(?P<units>[0-9-]{1,4})(?:[ ]*)"
								."(?P<teacher>.*) "
								."(?P<days>[MWFTShua]+)(?:[ ]*)"
								."(?P<start>[0-9p:]{4,6})-(?: |)"
								."(?P<end>[0-9:p]{4,6})(?:[ ]*)"
								."(?P<bldg>[*A-Z0-9]+)(?: |)"
								."(?P<room>[A-Z 0-9]+) (?:[ ]*)"
								."(?P<max>[0-9]{1,3})(?:[ ]*) "
								."(?P<enr>[0-9\/]+)(?:[ ]*) "
								."(?P<waitlist>[na0-9\/]+)(?:[ ]*) "
								."(?P<req>[0-9]{1,4})(?:[ ]*) "
								."(?P<nor>[0-9]{1,4})(?:[ ]*) "
								."(?P<rstr>[A-Za-z&]{0,5})(?:[ ]*) "
								."(?P<ead>Ead|)(?:[ ]*) "
								."(?P<status>Waitl|FULL|OPEN|NewOnly)"
								."$/",
								$line,
								$matches);
			if($found)
			{
			
				//print_r($matches);

				$CCODE = trim($matches['ccode']);
				$UNITS = trim($matches['units']);
				$SECT = trim($matches['sect']);
				$TEACHER = trim($matches['teacher']);
				$DAYS = getDays($matches['days']);
				$TYPE = trim($matches['type']);
				$STATUS = trim($matches['status']);

				// get the parent course type
				if(!$hasSetParent)
				{
					$GPARENT = $TYPE;
					$PARENT = "";
					$hasSetParent = true;
				}
				
				
				if( substr($matches['end'], -1, 1)=="p")
				{
					$matches['end'] = trim($matches['end'],"p");
					$parte = explode(":",$matches['end']);
					$parts = explode(":",$matches['start']);
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
					$parte = explode(":",$matches['end']);
					$parts = explode(":",$matches['start']);
					$END = mktime($parte[0],$parte[1],0,0,0,0);
					$START = mktime($parts[0],$parts[1],0,0,0,0);
				}
				
				$BLDG = trim($matches['bldg']);
				$ROOM = trim($matches['room']);
			
				if($TYPE!=$GPARENT)
				{
					//$PARENT = findParent($classes[$GPARENT]);
					$PARENT = $classes[$GPARENT][count($classes[$GPARENT])-1];
					//add the link
					$DB->dbinsert("temp_crslinks",array(
						'term'=>$TERM,
						'ccode'=>$PARENT,
						'childccode'=>$CCODE,
						));
				}


				$DB->dbinsert("temp_crsnames",array(
					'term'=>$TERM,
					'ccode'=>$CCODE,
					'dept'=>$dept,
					'crsnum'=>$cnum,
					'name'=>$name,
					));
				
				
				$DB->dbinsert("temp_ccode_status",array(
					'term'=>$TERM,
					'ccode'=>$CCODE,
					'status'=>$STATUS,
					));
				
				
				$classes[ $TYPE ][] = $CCODE;

				$DB->dbinsert("temp_courses",array(
					'term'=>$TERM,
					'ccode'=>$CCODE,
					'type'=>strtoupper($TYPE),
					'starttime'=>date("H:i:s", $START),
					'endtime'=>date("H:i:s", $END),
					'teacher'=>$TEACHER,
					'days'=>implode(",",$DAYS),
					'bldg'=>$BLDG,
					'room'=>$ROOM,
					));	
			
				//////////////////////////////////////////////
			}


		}//

	}

	//sleep( 50 );
	sleep(2);
}//foreach depts

$newtbls = array("ccode_status","courses","crsnames","crslinks");
foreach($newtbls as $nt)
{
	$DB->query("DELETE FROM ".$nt." WHERE term='".$TERM."'");
	$DB->query("INSERT INTO ".$nt." SELECT * FROM temp_".$nt);
}

// Update the term marker
$DB->dbreplace("term_updates",array(
'term'=>$TERM,
'lastupdate'=>date(MYSQL_DATETIME),
));

?>
