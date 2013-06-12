<?php

include "taskinc.php";

$data = sendRequest();

//echo $data;

$data = substr($data, strpos($data, '<select name="YearTerm">'));
$data = substr($data, 0, strpos($data,'</select>'));

$data = trim($data);

$lines = explode("\n", $data);
array_shift($lines);

$DB->temptbl("terms");

foreach($lines as $line)
{
	$line = trim($line);
	$line = str_replace('<option value="','',$line);
	$line = str_replace('">',',',$line);
		$line = str_replace("</option>","",$line);
	if( substr_count($line,"selected"))
	{
		$line = str_replace('" selected>', ',', $line);
		$line = str_replace('" selected="selected', '', $line);
		$line = $line.",1";
	}
	else
	{
		$line .= ",0";
	}

	$parts = explode(",",$line);
	echo $line."\n";
	$DB->dbinsert("temp_terms", array(
		'term'=>$parts[0],
		'name'=>$parts[1],
		'iscurrent'=>$parts[2],
		));

	//echo $line."\n";
}

$DB->query("TRUNCATE TABLE terms");
$DB->query("INSERT INTO terms SELECT * FROM temp_terms");

?>
