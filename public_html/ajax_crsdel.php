<?php

include "includes.php";

$SEARCHID= $_POST['searchid'];
$DEPT = $_POST['dept'];
$CNUM = $_POST['cnum'];
$ERROR = "0";

$SETTINGS = new Settings($SEARCHID);

$SETTINGS->delCourse($DEPT, $CNUM);
$SETTINGS->save();


//SELECT ccode FROM user_sections WHERE searchid='8' AND ccode IN (SELECT ccode FROM crsnames WHERE dept='WRITING' AND crsnum='39C')


header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");

header("Content-Type: application/json");

echo "{";
//hash,title,cnum,dept,error
echo "\"hash\":\"".$HASH."\",";
echo "\"error\":\"".$ERROR."\"";

echo "}";

die();




?>
