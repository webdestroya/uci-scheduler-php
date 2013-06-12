<?php

include "includes.php";




$SEARCHID = $_GET['searchid'];
$SECTS = explode("x",$_GET['sects']);

toolName("Schedule Details");

$tpl->set("searchid",$SEARCHID);

$USR = new Settings($SEARCHID);


function getCourseDisplay($sect,$term,$rowspan=1)
{
	global $DB, $MEM;

	/*
		SECT
	DEPT CNUM (TYPE)
	BLDG ROOM
	*/
	$data = "";
	$data .= $sect;
	$data .= "<br>";

	// get the dept,cnum
	$DB->query("SELECT dept,crsnum FROM crsnames WHERE term='".$term."' AND ccode='".$sect."'");
	$res = $DB->fetch_row();

	$data .= $res['dept']." ".$res['crsnum'];

	// get the type
	$DB->query("SELECT type,bldg,room FROM courses WHERE term='".$term."' AND ccode='".$sect."'");
	$res = $DB->fetch_row();

	$data .= " (".$res['type'].")";
	$data .= "<br>";

	$data .= $res['bldg']." ".$res['room'];
	
	$data2 = '<div class="coursebox">';
	$data2 .= '<div style="height:'.($rowspan*24).'px!important;">';
	$data2 .= $data;
	$data2 .= '</div>';
	$data2 .= '</div>';

	return $data2;
}



$TERM = $USR->getTerm();

// get the sects

$crsinfo = array();
foreach($SECTS as $sect)
{
	$temp = array();
	$DB->query("SELECT teacher,type,starttime,endtime,days FROM courses WHERE term='".$TERM."' AND ccode='".$sect."'");
	$res = $DB->fetch_row();
	
	$temp['teacher'] = $res['teacher'];
	$temp['type'] = $res['type'];
	$temp['days'] = $res['days'];

	$sparts = explode(":",$res['starttime']);
	$stm = mktime($sparts[0], $sparts[1],0);

	$eparts = explode(":",$res['endtime']);
	$etm = mktime($eparts[0], $eparts[1],0);

	$temp['start'] = date("g:ia",$stm);
	$temp['end'] = date("g:ia",$etm);

	$DB->query("SELECT status FROM ccode_status WHERE term='".$TERM."' AND ccode='".$sect."'");
	$temp['status'] = $DB->fetch_row("status");

	$DB->query("SELECT dept,crsnum FROM crsnames WHERE term='".$TERM."' AND ccode='".$sect."'");
	$res = $DB->fetch_row();
	
	$temp['cnum'] = $res['crsnum'];
	$temp['dept'] = $res['dept'];
	

	$crsinfo[] = $temp;
}
$tpl->set("crsinfo", $crsinfo);
//////////////////////////////////////////////


$USEDDAYS = array("M","Tu","W","Th","F");
$GCLASSES = array();

$SDAYS = array(1=>"M",2=>"Tu",3=>"W",4=>"Th",5=>"F",6=>"Sa",7=>"Su");
$SRDAYS = array_flip($SDAYS);

$calsearch = array();


$colors = array();
foreach($SECTS as $k=>$sect)
{
	// get the course info
	$DB->query("SELECT starttime, endtime, days FROM courses WHERE term='".$TERM."' AND ccode='".$sect."'");
	$res = $DB->fetch_row();
	
	$GCLASSES[ $sect ] = array();

	// set the days needed
	$GCLASSES[$sect]['days'] = explode(',',$res['days']);
	$USEDDAYS = array_merge($USEDDAYS, $GCLASSES[$sect]['days']);

	// set the start time
	$sparts = explode(":",$res['starttime']);
	$GCLASSES[$sect]['start'] = mktime($sparts[0], $sparts[1],0);

	// set the end time
	$eparts = explode(":",$res['endtime']);
	$GCLASSES[$sect]['end'] = mktime($eparts[0], $eparts[1],0);


	// set the color
	$colors[$sect] = "course".$k;
}


$longestclass = 0;
$earliestclass = 24;


foreach($SECTS as $sect)
{
	
	$data = $GCLASSES[$sect];

	$days = array();
	foreach($data['days'] as $v)
	{
		$calsearch[ $SRDAYS[$v] ][ ltrim(date("H.i",$data['start']),"0") ] = $sect;
		
		// what is the earliest class we have?
		if(date("H.i",$data['end'])>$longestclass)
		{
			$longestclass = ceil(date("H.i",$data['end']+600));
		}

		// whats the latest class we have?
		if(date("H.i",$data['start'])<$earliestclass)
		{
			$earliestclass = floor(date("H.i",$data['start']));
		}
	}
}


$DAY_S2L = array(
'M'=>"Monday",
'Tu'=>"Tuesday",
'W'=>"Wednesday",
'Th'=>"Thursday",
'F'=>"Friday",
'Sa'=>"Saturday",
'Su'=>"Sunday",
);


$days = array();
for($i=1;$i<8;$i++)
{
	if( in_array($SDAYS[$i], $USEDDAYS) )
	{

		$days[] = array(
		'day'=>$DAY_S2L[ $SDAYS[ $i ] ],
		'class'=> "",//in_array($SDAYS[$i], $USEDDAYS) ? "" : " hide",
		);
	}
}
$tpl->set("daysh", $days);



// calendar
$calendar = array();
$j=0;


$earliestclass--;
$longestclass++;

for($i=$earliestclass;$i<$longestclass;$i++)
{
	$calendar[$j]['time'] = $i.":00";

	for($d=1;$d<=7;$d++)
	{
		
		if( !in_array($SDAYS[$d], $USEDDAYS) )
		{
			$calendar[$j]['rowspan'.$d] = "";
			$calendar[$j]['crs'.$d] = "";
			$calendar[$j]['class'.$d] = "";
			$calendar[$j]['com'.$d.'s'] = "<!--";
			$calendar[$j]['com'.$d.'e'] = "-->";
		}
		else
		{

			$sect = false;
			$sect = $calsearch[$d][$i.".00"];
			if( $sect )
			{
				$dur = date("H.i",$GCLASSES[$sect]['end']+600) - date("H.i",$GCLASSES[$sect]['start']);
				$calendar[$j]['rowspan'.$d] = ceil($dur*2);
				$calendar[$j]['class'.$d] = $colors[$sect];
				
				if( (ceil($dur*2))%2!=0)
				{
					$calendar[$j]['class'.$d] .= " thour";
				}
				else
				{
					$calendar[$j]['class'.$d] .= " thhour";
				}
				
				$hideday[$d]=ceil($dur*2);
				
				$calendar[$j]['crs'.$d] = getCourseDisplay($sect,$TERM,$calendar[$j]['rowspan'.$d]);
				$calendar[$j]['com'.$d.'s'] = "";
				$calendar[$j]['com'.$d.'e'] = "";

			}
			else 
			{
				
				$hideday[$d]--;
				if($hideday[$d]>0)
				{
					$calendar[$j]['class'.$d] = "";
					$calendar[$j]['crs'.$d] = "";
					$calendar[$j]['rowspan'.$d] = "";
					$calendar[$j]['com'.$d.'s'] = "<!--";
					$calendar[$j]['com'.$d.'e'] = "-->";

				}
				else
				{
					$calendar[$j]['class'.$d] = "thour";
					$calendar[$j]['crs'.$d] = "&nbsp;";
					$calendar[$j]['rowspan'.$d] = "1";
					$calendar[$j]['com'.$d.'s'] = "";
					$calendar[$j]['com'.$d.'e'] = "";

				}
				
			}
		}
	}//d1-7
	

	$j++;
	

	$calendar[$j]['time'] = $i.":30";
	
	for($d=1;$d<=7;$d++)
	{
		if( !in_array($SDAYS[$d], $USEDDAYS) )
		{
			$calendar[$j]['rowspan'.$d] = "";
			$calendar[$j]['crs'.$d] = "";
			$calendar[$j]['com'.$d.'s'] = "<!--";
			$calendar[$j]['com'.$d.'e'] = "-->";
			$calendar[$j]['class'.$d] = "";
		}
		else
		{

			$sect = false;
			$sect = $calsearch[$d][$i.".30"];
			if( $sect )
			{
				$dur = date("H.i",$GCLASSES[$sect]['end']) - date("H.i",$GCLASSES[$sect]['start']);
				$calendar[$j]['rowspan'.$d] = ceil($dur*2);
				$hideday[$d]=ceil($dur*2);
				$calendar[$j]['class'.$d] = $colors[$sect]." thhour";
				$calendar[$j]['crs'.$d] = getCourseDisplay($sect,$TERM,$calendar[$j]['rowspan'.$d]);
				$calendar[$j]['com'.$d.'s'] = "";
				$calendar[$j]['com'.$d.'e'] = "";
			}
			else 
			{		
				$hideday[$d]--;
				if($hideday[$d]>0)
				{
					$calendar[$j]['class'.$d] = "";
					$calendar[$j]['rowspan'.$d] = "";
					$calendar[$j]['com'.$d.'s'] = "<!--";
					$calendar[$j]['com'.$d.'e'] = "-->";
					$calendar[$j]['crs'.$d] = "";
				}
				else
				{
					$calendar[$j]['class'.$d] = "thhour";
					$calendar[$j]['rowspan'.$d] = "1";
					$calendar[$j]['com'.$d.'s'] = "";
					$calendar[$j]['com'.$d.'e'] = "";
					$calendar[$j]['crs'.$d] = "&nbsp;";
				}

			}
		}
	}//d1-7
	
	$j++;
	
}

$tpl->set("calendar",$calendar);





$tpl->toBrowser();

?>
