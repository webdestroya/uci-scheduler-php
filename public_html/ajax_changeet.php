<?php

include "includes.php";

$SEARCHID= $_POST['searchid'];
$ET = $_POST['endtime'];

$SETTINGS = new Settings($SEARCHID);
$SETTINGS->setEndTime($ET);
$SETTINGS->save();

redirect("/search/".$SEARCHID);

?>
