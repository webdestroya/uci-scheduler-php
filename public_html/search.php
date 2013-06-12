<?php


include "includes.php";


toolName("Search Schedules");



if($_GET['id']>0)
{
	$SETTINGS = new Settings($_GET['id']);

	// try and update the cookie marker
	if($_COOKIE['s']!="")
	{
		$DB->dbreplace("user_cookies", array(
		'searchid'=>$SETTINGS->getID(),
		'sessionid'=>$_COOKIE['s'],
		));
	}

}
else
{
	// make a new search
	$SETTINGS = new Settings();

	$searchid = $SETTINGS->getID();

	// save it for their cookie
	if($_COOKIE['s']!="")
	{
		$DB->dbreplace("user_cookies", array(
		'searchid'=>$searchid,
		'sessionid'=>$_COOKIE['s'],
		));
	}
	$SETTINGS->save();

	// redirect with the new term
	redirect("/search/".$searchid);
}


// set search id
$tpl->set("searchid", $SETTINGS->getID() );

// set current term
$tpl->set("termid", $SETTINGS->getTerm() );

$start = $SETTINGS->getStartTime();
$endtime = $SETTINGS->getEndTime();

$tpl->set("sta", $start);
$tpl->set("eta", $endtime);

// start time pretty
$pstart = explode(":",$start);
array_pop($pstart);
$pstart[0] = ltrim($pstart[0],"0");
if($pstart[0]>12)
{
	$pstart[0] = $pstart[0] - 12;
	$startt = $pstart[0].":".$pstart[1]." pm";
}
else
{
	$startt = $pstart[0].":".$pstart[1]." am";
}

// end time pretty
$pend = explode(":",$endtime);
array_pop($pend);
$pend[0] = ltrim($pend[0],"0");
if($pend[0]>12)
{
	$pend[0] = $pend[0] - 12;
	$endt = $pend[0].":".$pend[1]." pm";
}
else
{
	$endt = $pend[0].":".$pend[1]." am";
}

$tpl->set("starttime", $startt );
$tpl->set("endtime", $endt );

// make the days look pretty
$days = $SETTINGS->getDays();
$daysarr = $days;
$daysarr = str_replace("M","Mon",$daysarr);
$daysarr = str_replace("Tu","Tue",$daysarr);
$daysarr = str_replace("W","Wed",$daysarr);
$daysarr = str_replace("Th","Thu",$daysarr);
$daysarr = str_replace("F","Fri",$daysarr);
$daysarr = str_replace("Sa","Sat",$daysarr);
$daysarr = str_replace("Su","Sun",$daysarr);
$tpl->set("days", implode(", ",$daysarr) );

$tpl->set("daysa", implode('","',$SETTINGS->getDays()) );
$tpl->set("typesa", implode('","',$SETTINGS->getTypes()) );

// set the types in color
$types = $SETTINGS->getTypes();
foreach($types as $k=>$v)
{
	switch($v)
	{
		case "Waitl":
			$types[$k] = '<span class="red">Waitl</span>';
			break;
		case "OPEN":
			$types[$k] = '<span class="green">OPEN</span>';
			break;
		case "FULL":
			$types[$k] = '<span class="black">FULL</span>';
			break;
		case "NewOnly":
			$types[$k] = '<span class="blue">NewOnly</span>';
			break;
	}
}
$tpl->set("types", implode(", ",$types));

// The terms
$terms = array();
$DB->query("SELECT term,name FROM terms WHERE term IN (SELECT term FROM courses) ORDER BY term DESC");
foreach($DB->fetch_assoc() as $res)
{
	// is this the current term?
	if($res['term'] == $SETTINGS->getTerm())
	{
		$termstr = $res['name'];
		$termstr = str_replace("Qtr", "Quarter", $termstr);
		$tpl->set("termname", $termstr);
	}

	$terms[] = array(
		'term'=>$res['term'],
		'name'=>$res['name'],
		);
}
$tpl->set("terms", $terms);


// list of departments
$depts = array();
$DB->query("SELECT dept,name FROM depts ORDER BY dept ASC");
foreach($DB->fetch_assoc() as $res)
{
	$depts[] = array(
		'dept'=>$res['dept'],
		'name'=>$res['name'],
		);
}
$tpl->set('depts', $depts);


// The course selections
$curcrs = $SETTINGS->getCourses();
if( count($curcrs)>0)
{
	$courses = array();
	
	foreach($curcrs as $crs)
	{
		$DB->query("SELECT DISTINCT name FROM crsnames WHERE term='".$SETTINGS->getTerm()."' AND dept='".$crs['dept']."' AND crsnum='".$crs['cnum']."' LIMIT 0,2");
		if($DB->get_num_rows()==1)
		{
			$title = str_replace('"',"", $DB->fetch_row("name"));
		}
		else
		{
			$title = ' COURSE TITLES VARY (Use Force Sections to specify the class you want.)';
		}

		$courses[] = array(
			'dept'=>$crs['dept'],
			'cnum'=>$crs['cnum'],
			'title'=>$title,
			'hash'=>md5($crs['dept'].$crs['cnum']),
		);
	}

	$tpl->set("courses",$courses);
}


// Get any section restrictions
$curccs = $SETTINGS->getSections();
if( count($curccs)>0)
{
	$ccodes = array();
	
	foreach($curccs as $crs)
	{
		$DB->query("SELECT name,dept,crsnum FROM crsnames WHERE term='".$SETTINGS->getTerm()."' AND ccode='".$crs."' LIMIT 0,1");
		$res = $DB->fetch_row();
		
		$DB->query("SELECT type FROM courses WHERE term='".$SETTINGS->getTerm()."' AND ccode='".$crs."' LIMIT 0,1");
		$res['type'] = $DB->fetch_row("type");


		$ccodes[] = array(
		'dept'=>$res['dept'],
		'cnum'=>$res['crsnum'],
		'title'=>$res['name'],
		'type'=>$res['type'],
		'ccode'=>$crs,
		);
	}

	$tpl->set("ccodes",$ccodes);
}



$tpl->toBrowser();

?>
