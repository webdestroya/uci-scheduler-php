<?php

include "includes.php";

$DB->query("SELECT searchid FROM user_cookies WHERE sessionid='".$_COOKIE['s']."'");
if($DB->get_num_rows())
{
	$sid = $DB->fetch_row("searchid");
	redirect("/search/".$sid);
}
else
{
	header("Location: /search");
	die("Redirect");
}

?>
