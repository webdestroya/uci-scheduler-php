<?php

include "includes.php";

$DEPT = $_POST['dept'];
$CNUM = strtoupper($_POST['cnum']);
$SEARCHID= $_POST['searchid'];
$TITLE = "";
$HASH = "";
$ERROR = "0";

$SETTINGS = new Settings($SEARCHID);

$TERM = $SETTINGS->getTerm();

// Lookup the course
$DB->query("SELECT DISTINCT name FROM crsnames WHERE 1 "
			."AND term='".$TERM."' "
			."AND dept='".$DEPT."' "
			."AND crsnum='".$CNUM."' "
			."LIMIT 0,2 "
		);
if($DB->get_num_rows())
{
    if($DB->get_num_rows()==1)
    {
    	$TITLE = str_replace('"',"&quot;", $DB->fetch_row("name"));
    }
    else
    {
		$TITLE = ' COURSE TITLES VARY (Use Force Sections to specify the class you want.)';
    }

    $SETTINGS->addCourse($DEPT,$CNUM);
    $SETTINGS->save();
}
else
{
	$ERROR = "1";
}

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");

header("Content-Type: application/json");

$HASH = md5($DEPT.$CNUM);


echo "{";
//hash,title,cnum,dept,error
echo "\"dept\":\"".$DEPT."\",";
echo "\"cnum\":\"".$CNUM."\",";
echo "\"title\":\"".$TITLE."\",";
echo "\"hash\":\"".$HASH."\",";
echo "\"error\":\"".$ERROR."\"";

echo "}";

die();




?>
