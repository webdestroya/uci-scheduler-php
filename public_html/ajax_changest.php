<?php

include "includes.php";

$SEARCHID= $_POST['searchid'];
$ST = $_POST['starttime'];

$SETTINGS = new Settings($SEARCHID);
$SETTINGS->setStartTime($ST);
$SETTINGS->save();

redirect("/search/".$SEARCHID);

?>
