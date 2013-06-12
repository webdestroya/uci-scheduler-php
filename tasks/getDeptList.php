<?php

include "taskinc.php";

$data = sendRequest();


$data = substr($data, strpos($data, '<select name="Dept">'));
$data = substr($data, 0, strpos($data,'</select>'));

$data = trim($data);

$data = str_replace("&amp;", "&", $data);

$lines = explode("\n", $data);
array_shift($lines);
array_shift($lines);

$DB->temptbl("depts");

foreach($lines as $line)
{
	$line = trim($line);
	if(!substr_count($line, 'color: gray') )
	{
		if(preg_match("/>(?P<Short>[A-Z&0-9- ]+)(?:[\. ]+)(?P<Name>['A-Z0-9a-z -,\(\)\/;]+)<\/option>$/", $line, $match))
		{
			$short = trim($match['Short']);
			$name = trim($match['Name']);
			echo $short."|".$name;
			echo "\n";
			$DB->dbinsert("temp_depts",array(
				'dept'=>$short,
				'name'=>$name,
				));
		}
		//print_r($match);


		//echo $line."\n";
	}
	// INSERT INTO DB
	// currentterm ( termid )
	// --- select db first, ONLY insert new terms

}

$DB->query("TRUNCATE TABLE depts");
$DB->query("INSERT INTO depts SELECT * FROM temp_depts");


?>
