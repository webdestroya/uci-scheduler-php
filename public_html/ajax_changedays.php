<?php

include "includes.php";

$SEARCHID= $_POST['searchid'];
if(isset($_POST['days']))
{
    $DAYS = $_POST['days'];
}
else
{
    $DAYS = array("M","Tu","W","Th","F");
}

$SETTINGS = new Settings($SEARCHID);
$SETTINGS->setDays($DAYS);
$SETTINGS->save();

redirect("/search/".$SEARCHID);

?>
