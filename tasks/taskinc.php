<?php

$incpath = array(
	".",
	"../classes",
	"../config",	
);

ini_set("include_path", implode(":",$incpath) );
ini_set("display_errors", "on");
include "csdsexception.obj.php";
include "csdscheduler.obj.php";

// Configuration Files
include "database.conf.php";
include "general.conf.php";
include "images.conf.php";

include "lib/memcache.lib.php";
include "lib/websoc.lib.php";
include "lib/template.lib.php";
include "lib/functions.lib.php";
include "lib/skin.lib.php";
include "lib/display.lib.php";


//connect to memcache
$MEM = new CSDMemcache("sched_");
global $MEM;


function __autoload($name)
{
	require_once strtolower($name).".obj.php";
}

// exception handling
function exception_handler($exception) 
{
	echo $exception->getMessage();
	die();
}
set_exception_handler('exception_handler');


?>
