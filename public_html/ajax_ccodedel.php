<?php

include "includes.php";

$SEARCHID= $_POST['searchid'];
$CCODE = $_POST['ccode'];
$ERROR = "0";

$SETTINGS = new Settings($SEARCHID);

$SETTINGS->delCcode($CCODE);
$SETTINGS->save();

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");

header("Content-Type: application/json");

echo "{";
//hash,title,cnum,dept,error
echo "\"ccode\":\"".$CCODE."\",";
echo "\"error\":\"".$ERROR."\"";

echo "}";

die();




?>
