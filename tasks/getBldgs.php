<?php

include "taskinc.php";

include "buildings.php";


//'ACE'=>array('x'=>"4",'y'=>"5",'abbr'=>"ACE",'num'=>"522",'name'=>"Arts Computation Engineering Trailers",),


foreach($BUILDINGS as $k=>$v)
{
	$DB->dbreplace("buildings",array(
	'bldg'=>$k,
	'name'=>$v['name'],
	'num'=>$v['num'],
	'xpos'=>$v['x'],
	'ypos'=>$v['y'],
	));
}


?>
