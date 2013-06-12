<?php
include "includes.php";

$SEARCHID = $_GET['id'];

$USR = new Settings($SEARCHID);

if($USR->getID()!=$SEARCHID)
{
	redirect("/search/".$USR->getID());
}

$tpl->set("searchid",$SEARCHID);

toolName("Schedule Results");

$tpl->toBrowser();

?>
