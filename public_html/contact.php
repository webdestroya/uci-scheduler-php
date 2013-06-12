<?php

include "includes.php";

if($_POST['action']=="feedback")
{
	$msg = $_POST['msg'];
	$msg = str_replace("\r","",$msg);
	$msg = stripslashes($msg);
	
	$headers = "";
	if( preg_match("/([a-zA-Z0-9_]+)@([a-z-0-9]+)\.([a-zA-Z0-9-\.]+)/mi", $msg, $match) )
	{
		$email = $match[0];

		$headers .= "Reply-to: ".$email."\n";
	}

	mail("contact@scheduler.com", "Schedule Builder Feedback", $msg, $headers);

	$_SESSION['success_msg'] = "Your message has been successfully sent! If you provided an email or IM, you should receive a response within a day or so.";

	redirect("/contact");
}

toolName("Contact");

$tpl->toBrowser();

?>
