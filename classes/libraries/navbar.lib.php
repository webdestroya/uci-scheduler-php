<?php

/**
  * Navbar Libraries
  * @package ANHSLibraries
  * @subpackage Navbar
  * @filesource
  */



function ParseNavbarFile($section, $navbar=array())
{
	global $DB;
	
	$DB->query( "SELECT name,url,target,type FROM tbl_navbar WHERE section='".$section."' ORDER BY `order` ASC" );
	while($res = $DB->fetch_assoc())
	{
		if($res['type']=="header")
		{
			//header
			$navbar[] = array($res['name'], false,"header level2");
		}
		else 
		{
			// not header
			if($res['target']=="_blank")
			{
				$navbar[] = array( $res['name'], $res['url'],"_blank" );
			}
			else
			{
				$navbar[] = array( $res['name'], $res['url'] );
			}
		}
	}
	
	return $navbar;
}

function GenerateSideNav($array)
{
	global $DB, $tpl;
	$text = array();
	foreach( $array as $link )
	{
		/*
		0 = name, 1=link, 2=class
		*/
		$class = "";
		if( $_SERVER["SCRIPT_NAME"]==$link[1])
		{
			$class = " class='active'";
		}
		if( $_SERVER["SCRIPT_NAME"]."?".$_SERVER['QUERY_STRING'] == $link[1] )
		{
			$class = " class='active'";
		}
		if( $_SERVER['REQUEST_URI'] == $link[1] )
		{
			$class = " class='active'";
		}
		
		$target = "";
		if( $link[3] )
		{
			$target = ' target="_blank"';
		}
		/*elseif( !substr_count($link[1],".php") )
		{	
			// its not a php link
			$target = ' target="_blank"';
		}*/
		
		if( substr_count($link[1],".pdf") )
		{	
			// its not a php link
			$target = ' target="_blank"';
			$link[0] .= ' <img src="/img.php/m/16/pdf" width="10" height="10" alt="PDF" />';
			$tpl->set("show_pdf",true);
		}
			
		$class = $link[2] ? ' class="'.$link[2].'"' : $class;
		$inside = $link[1] ? '<a href="'.$link[1].'"'.$target.'>'.$link[0].'</a>' : $link[0];
		$text[] = array('link'=>"<li".$class.">".$inside."</li>");
		
	}//
	return $text;
}

?>