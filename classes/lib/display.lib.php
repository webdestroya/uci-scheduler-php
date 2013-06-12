<?php


function dprint($arr)
{
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function redirect($url,$server="www2")
{
	//header("Location: http://".$server.".picmember.com".$url);
	header("Location: ".$url);
	die("Redirecting...");
}

function toolName($name)
{
	global $tpl;
	$tpl->set("g_tool_name",$name);
	$tpl->set("gtitle",$name);
}
function toolIcon($icon)
{
	global $tpl;
	$tpl->set("g_tool_icon",$icon);
}

function show_date_select($name="date",$mon=false,$day=false,$year=false)
{
	global $tpl;

	$mon = $mon ? $mon : date("n");
	$day = $day ? $day : date("j");
	$year = $year ? $year : date("Y");
	
	$sel = $mon."-".$day."-".$year;

	$data = "";
	$data .= '<input type="hidden" id="'.$name.'m" value="'.$mon.'" name="'.$name.'[m]">';
	$data .= '<input type="hidden" id="'.$name.'d" value="'.$day.'" name="'.$name.'[d]">';
	$data .= '<input type="hidden" id="'.$name.'y" value="'.$year.'" name="'.$name.'[y]">';

	$data .= '<div id="ajaxcalendar">Loading calendar...</div>';

	$fs = 'new Ajax.Request("/ajax/calendar.php", {method:"post", postBody:"n='.$name.'&m='.$mon.'&d='.$day.'&y='.$year.'&se='.$sel.'", ';
	$fs .= 'onSuccess: function(t){ $(\'ajaxcalendar\').innerHTML=t.responseText; } });';

	$tpl->set("footer_script",$fs);

	return $data;
}

function _removeDotFiles($var)
{
	return substr($var,0,1)!=".";
}
function removeDotFiles($arr)
{
	return array_filter($arr, "_removeDotFiles");
}

function show_error_page($msg,$showback=true,$title="Whoops")
{
	global $tpl;
	
	$back = "";
	if($showback)
	{
		$back = '<p>To return to the page you were last viewing, <a href="'.$_SESSION['lastpage'].'">click here</a>.</p>';
	}
	
	$error = '<div class="mb_wrapper"><div class="mb_error"><h1>'.$title.'</h1><p>'.$msg.'</p>'.$back.'</div></div>';
	$tpl->setTemplate("/error.tpl");
	$tpl->set("gerror_msg",$error);
	$tpl->toBrowser();
}

function show_error($msg,$title="Whoops")
{
	global $tpl;
	$error = '<div class="mb_wrapper"><div class="mb_error"><h1>'.$title.'</h1><p>'.$msg.'</p></div></div>';
	$tpl->set("gerror_msg",$error);
}

function show_notice($msg,$title="Notice")
{
	global $tpl;
	$error = '<div class="mb_wrapper"><div class="mb_info"><h1>'.$title.'</h1><p>'.$msg.'</p></div></div>';
	$tpl->set("gnotice_msg",$error);
}


function show_warning($msg,$title="Warning")
{
	global $tpl;
	$warning = '<div class="mb_wrapper"><div class="mb_warning"><h1>'.$title.'</h1><p>'.$msg.'</p></div></div>';
	$tpl->set("gwarning_msg",$warning);
}

function show_success($msg,$title="Success")
{
	global $tpl;
	$success = '<div class="mb_wrapper"><div class="mb_success"><h1>'.$title.'</h1><p>'.$msg.'</p></div></div>';
	$tpl->set("gsuccess_msg",$success);
}

function needSALibs($lib1=false,$lib2=false,$lib3=false,$lib4=false,$lib5=false)
{
	global $tpl;

	$libs = array();
	$libs[] = $lib1;
	if($lib2){$libs[] = $lib2;}
	if($lib3){$libs[] = $lib3;}
	if($lib4){$libs[] = $lib4;}
	if($lib5){$libs[] = $lib5;}

	$tpl->set("scriptaculous_libs", implode(",",$libs));
}

function cancel_button($url="/home.php",$text=false,$button="Cancel")
{
	global $tpl;
	
	$txt = "Are you sure you want to cancel?";
	if($text)
	{
		$txt = $text;
	}
	
	$cancel = '<input type="button" style="margin-left:5px;" class="button" ';
	$cancel .= "onclick=\"return (new pop_dialog()).show_form('Confirm Cancel','<p>".$txt."</p>','Yes|No','".$url."');\" ";
	$cancel .= 'name="cancel" value="'.$button.'">';
	$tpl->set("cancel",$cancel);
}
function cancel_button_url($url="/home.php",$button="Cancel")
{
	global $tpl;
	
	$cancel = '<input type="button" style="margin-left:5px;" class="cancel" ';
	$cancel .= "onclick=\"goURI('".$url."');\" ";
	$cancel .= 'name="cancel" value="'.$button.'">';
	$tpl->set("cancel",$cancel);
}

function sectionIndexPage($section)
{
	global $DB;

	if( file_exists( SITE_FILES.$section."/pages/.firstpage") )
	{
		redirect( file_get_contents(SITE_FILES.$section."/pages/.firstpage"));
	}
	else
	{
		show_error_page("Sorry, but this section has not yet been setup!");
	}
}

function getFileTitle($sect, $file)
{
	$data = trim(file_get_contents(SITE_FILES.$sect."/pages/.titles") );
	$title = "";

	$lines = explode("\n", $data);
	foreach($lines as $line)
	{
		$parts = explode("\t", $line);

		if($parts[0]==$file)
		{
			$title = $parts[1];
			break;
		}
	}

	return $title;
}



function extToMime($mime)
{
	global $DB;

	$DB->query("SELECT mimetype FROM mimeinfo WHERE ext='".$mime."' LIMIT 0,1");
	if($DB->get_num_rows())
	{
		$data = $DB->fetch_row("mimetype");
	}
	else
	{
		$data = "application/octet-stream";
	}
	return $data;
}

function mimeToEnglish($mime)
{
	global $DB;

	$DB->query("SELECT info FROM mimeinfo WHERE ext='".$mime."' OR mimetype='".$mime."' LIMIT 0,1");
	if($DB->get_num_rows())
	{
		$data = $DB->fetch_row("info");
	}
	else
	{
		$data = "Unknown File Type";
	}
	return $data;
}
 
function extToMIMEImage($mime)
{
	global $DB;
	$DB->query("SELECT image FROM mimeinfo WHERE ext='".$mime."' OR mimetype='".$mime."' LIMIT 0,1");
	if($DB->get_num_rows())
	{
		$data = $DB->fetch_row("image");
	}
	else
	{
		$data = "application";
	}
	return $data;

}


function extToImage($ext)
{
	extToMIMEImage($ext);
}


function parseWebsiteNavbar($data)
{
	$headers = array();
	$links = array();
	$linkstmp = array();
	$data = trim($data);
	$lines = explode("\n",$data);

	for($i=0;$i<count($lines);$i++)
	{
		$line = $lines[$i];
		if( substr($line,0,1)!="#")
		{
			$parts = explode("\t",$line);
			if($parts[0]=="1")
			{
				// if we already have a header
				if( count($linkstmp)>0)
				{
					$links[] = $linkstmp;
					$linkstmp = array();
				}

				// main link
				$headers[] = array( 'name'=>$parts[1], 'link'=>$parts[2] );
			}
			else
			{
				// sublink
				$linkstmp[] = array( 'name'=>$parts[1], 'link'=>$parts[2]);
			}
		}
	}
	
	if( count($linkstmp) > 0)
	{
		$links[] = $linkstmp;
	}
	
	return array( $headers, $links);
}


function getJsUserList()
{
	global $DB, $MEM;

	$data = $MEM->get("js_userlist");
	if(!$data)
	{
		$aUsers = array();
		$aInfo = array();

		$DB->query("SELECT n.username, s.fname, s.lname FROM staff s LEFT JOIN users n ON (n.id=s.staffid) WHERE n.username IS NOT NULL ORDER BY n.username ASC");
		foreach($DB->fetch_assoc() as $res)
		{
			$aInfo[] = $res['fname']." ".$res['lname'];
			$aUsers[] = strtolower($res['username']);
		}
		$DB->query("SELECT n.username, s.fname, s.lname FROM students s LEFT JOIN users n ON (n.id=s.permid) WHERE n.username IS NOT NULL ORDER BY n.username ASC");
		foreach($DB->fetch_assoc() as $res)
		{
			$aInfo[] = $res['fname']." ".$res['lname'];
			$aUsers[] = strtolower($res['username']);
		}
			
		$aResults = array();
		$count = 0;
		for ($i=0;$i<count($aUsers);$i++)
		{
			$count++;
			$aResults[] = array(
				"value"=>htmlspecialchars($aUsers[$i]),
				"info"=>htmlspecialchars($aInfo[$i])
			);
		}
		$arr = array();
		for ($i=0;$i<count($aResults);$i++)
		{   
			$arr[] = "{\"value\":\"".$aResults[$i]['value']."\",\"info\":\"".$aResults[$i]['info']."\"}";
		}
		$data = "[".implode(",", $arr)."]";
		
		$MEM->set("js_userlist", $data);
	}
	return $data;
}



/*

HTML Treemap Implementation
by Geoff Gaudreault
http://www.neurofuzzy.net

This is provided AS-IS with no warranty express or implied.
Use this code, or any code that I write, at your own risk.

Good luck, starfighter!

*/

 
//
//	Function: render_treemap
//	Recursive function that returns an HTML treemap based on an list of items
//
//	Parameters:
//	$theArray - treemapped items; associative array where key is the item name and value is the quantity
//	$width - the width of the treemap
//	$height - the height of the treemap
//	$depth - the current recursion depth, starts at 0
//	$orientation - 0 starts dividing vertically, 1 starts dividing horizontally.  This basically swaps the aspect ratio of the cells
function render_treemap ($theArray, $width, $height, $depth = 0, $orientation = 0)
{
	
	// base url of links
	global $baseurl;
	
	// CELL COLORING (optional)
	// ------------------------
	//
	// secondary associative array where key is the item name and the value is the string date of the item.
	// this is used to alter the item's color based on it's age
	global $taggedArray;
	
	// the age of the newest item
	global $towhen;
	// the amount of time dilation.  This parameter alters time, speeds up the harvest, and teleports you off this rock.
	global $timesquash;
	// ------------------------
	
	
	// if starting, start with the opening treemap tag
	if ($depth == 0)
	{
		$html = '<div class="treemap" style="width:'.$width.'px;height:'.$height.'px;">';
	}
	else
	{
		$html = "";
	}
	
	
	// continue to chunk this array in halves until you are left with chunks of 1 item.
	// a chunk of 1 item is the cell
	if (count($theArray) > 1)
	{
	
		$splitArray = array_chunk($theArray,ceil(count($theArray) / 2),true);
		
		$a = $splitArray[0];
		$b = $splitArray[1];
		
		$apercent = array_sum($a) / array_sum($theArray);
		$bpercent = 1 - $apercent;
		
		// swap division horizontal/vertical depending on orientation
		if ($depth % 2 == $orientation)
		{
			$awidth = ceil($width * $apercent);
			$bwidth = $width - $awidth;
			
			$aheight = $height;
			$bheight = $height;
		}
		else
		{
			$aheight = ceil($height * $apercent);
			$bheight = $height - $aheight;
			
			$awidth = $width;
			$bwidth = $width;
		}
		
		$astyle = "width:".$awidth."px;height:".$aheight."px;";
		$bstyle = "width:".$bwidth."px;height:".$bheight."px;";
		
		$html .= '<div class="node" style="'.$astyle.'">';
		
		// recurse on child a
		$html .= render_treemap($a, $awidth, $aheight, $depth + 1);
		
		$html .= "</div>";
		
		// recurse on child b
		$html .= "<div class=\"node\" style=\"$bstyle\">";
		
		$html .= render_treemap($b, $bwidth, $bheight, $depth + 1);
		
		$html .= "</div>";
	
	}
	else
	{
	
		// make cell
		foreach( $theArray as $tag => $pop )
		{
			$urltag = strtolower(str_replace(" ","-",trim($tag)));
			if (strpos($urltag,"-"))
			{
				$classtext = " proper";
			}
			else
			{
				$classtext = "";
			}
			
			// age coloring
			if (isset($taggedArray))
			{
				if (!isset($towhen))
				{
					$now = strtotime("now");
				}
				else
				{
					$now = strtotime($towhen);
				}
				
				$latest = strtotime($taggedArray[$tag]);
				
				$age = $now - $latest;
				
				if (!isset($timesquash))
				{
					$timesquash = 1;
				}
				
				$age = floor($age / (8640 / $timesquash));
				
				if ($age > 6) 
				{
					if (strlen($classtext) == 0) 
					{
						$r = min(180,164 + $age);
						$g = min(190,192 + $age);
						$b = min(205,136 + $age);
					}
					else
					{
						$r = min(204,204 + $age);
						$g = min(190,131 + $age);
						$b = min(180,73 + $age);
					}
					$col = dechex($r).dechex($g).dechex($b);
					$styletext = " background-color: #$col";
				}
				else
				{
					$styletext = "";
				}
			}
			else
			{
				$styletext = "";
			}
			
			// change text size depending on size of cell
			$textsize = max(10,floor(($width - 16) / max(8,strlen($tag))));
			$textsize = max(10,min($textsize, $height - 8));
			
			$styletext = "style=\" font-size: {$textsize}px; $styletext;\"";
			
			// make html
			$html .= "<a class=\"textnode$classtext\"$styletext href=\"".$baseurl."find/$urltag\" title=\"View stories with keyword '$tag'\"><img src=\"".$baseurl."chrome/spacer.gif\" height=\"100%\" width=\"1\" border=\"0\" alt=\"\" />$tag</a>";
		}
	}
	
	// close treemap
	if ($depth == 0)
	{
		$html .= '</div>';
	}
	
	return $html;

}
?>
