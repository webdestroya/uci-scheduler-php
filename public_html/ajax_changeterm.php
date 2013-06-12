<?php

include "includes.php";

$SEARCHID= $_POST['searchid'];
$TERM = $_POST['term'];

$SETTINGS = new Settings($SEARCHID);
$SETTINGS->setTerm($TERM);
$SETTINGS->save();

redirect("/search/".$SEARCHID);

?>
