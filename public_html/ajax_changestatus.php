<?php

include "includes.php";

$SEARCHID= $_POST['searchid'];
if(isset($_POST['types']))
{
    $TYPES = $_POST['types'];
}
else
{
    $TYPES = array("OPEN");
}

$SETTINGS = new Settings($SEARCHID);
$SETTINGS->setTypes($TYPES);
$SETTINGS->save();

redirect("/search/".$SEARCHID);

?>
