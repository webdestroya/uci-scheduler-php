<?php

include "includes.php";

$CCODE = $_POST['ccode'];
$SEARCHID= $_POST['searchid'];
$DEPT = "";
$CNUM = "";
$TITLE = "";
$HASH = "";
$TYPE = "";
$ERROR = "0";

$SETTINGS = new Settings($SEARCHID);

$TERM = $SETTINGS->getTerm();

// Lookup the course
$DB->query("SELECT dept,crsnum,name FROM crsnames WHERE 1 "
			."AND term='".$TERM."' "
			."AND ccode='".$CCODE."' "
			."LIMIT 0,1 "
		);
if($DB->get_num_rows())
{
    $res = $DB->fetch_row();
	$TITLE = str_replace('"',"&quot;", $res['name'] );
	$DEPT = str_replace('"',"&quot;", $res['dept'] );
	$CNUM = str_replace('"',"&quot;", $res['crsnum'] );


    $DB->query("SELECT type FROM courses WHERE term='".$TERM."' AND ccode='".$CCODE."' LIMIT 0,1 ");
	$TYPE = $DB->fetch_row("type");

    $ret = $SETTINGS->addCcode($CCODE);
    if($ret)
    {
        $SETTINGS->save();
    }
    else
    {
        $ERROR = "2";
    }
}
else
{
	$ERROR = "1";
}

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");

header("Content-Type: application/json");


echo "{";
//hash,title,cnum,dept,error
echo "\"dept\":\"".$DEPT."\",";
echo "\"cnum\":\"".$CNUM."\",";
echo "\"title\":\"".$TITLE."\",";
echo "\"type\":\"".$TYPE."\",";
echo "\"ccode\":\"".$CCODE."\",";
echo "\"error\":\"".$ERROR."\"";

echo "}";

die();




?>
