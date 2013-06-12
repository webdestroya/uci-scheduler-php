<?php

//include "csdsexception.obj.php";
include "csdscheduler.obj.php";

// Configuration Files
include "database.conf.php";
include "general.conf.php";
include "images.conf.php";

include "lib/memcache.lib.php";
include "lib/template.lib.php";
include "lib/functions.lib.php";
include "lib/skin.lib.php";
include "lib/display.lib.php";

ini_set("display_errors", "on");

//connect to memcache
$MEM = new CSDMemcache("csdhs_");
global $MEM;


function __autoload($name)
{
	require_once strtolower($name).".obj.php";
}

// exception handling
function exception_handler($exception) 
{
	show_error_page("<pre>".$exception->getMessage()."</pre>","Exception");
}
set_exception_handler('exception_handler');


// template handler
$tpl = new Template($_SERVER['DOCUMENT_ROOT']."/templates/");

// make the session
//$SESSION = new Session($_COOKIE['cse']);

// some message settings
if(isset($_SESSION['error_msg']))
{
	show_error($_SESSION['error_msg'],"Whoops");
	unset($_SESSION['error_msg']);
}
if(isset($_SESSION['success_msg']))
{
	show_success($_SESSION['success_msg'],"Success");
	unset($_SESSION['success_msg']);
}
if(isset($_SESSION['notice_msg']))
{
	show_notice($_SESSION['notice_msg'],"Notice");
	unset($_SESSION['notice_msg']);
}
if(isset($_SESSION['warning_msg']))
{
	show_warning($_SESSION['warning_msg'],"Warning");
	unset($_SESSION['warning_msg']);
}


// Make some fancy footer references
/*$file = ltrim($_SERVER['REQUEST_URI'],"/");
if(substr_count($file,"?"))
{
	$file = substr($file,0,strpos($file,"?"));
}

$tpl->set("global_request",$file);
*/
$tpl->set("global_lastupdate",date("n/d/Y"));

// Show the URCHIN webstats
/*if(GOOGLE_URCHIN_KEY)
{
	$tpl->set("has_google_urchin",true);
}
*/

?>
