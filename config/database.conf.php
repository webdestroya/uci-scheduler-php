<?php

include_once "lib/db/database.db.php";
include_once "lib/db/mysqli.db.php";


$DB = new DB_MySQLi();
$DB->setDBName("scheduler");
$DB->setHostname("localhost");
$DB->setPassword("password");
$DB->setUsername("username");

global $DB;

?>
