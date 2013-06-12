<?php


// pulls the course information from WebSOC
function getCourses($dept,$term,$range=false)
{
	$query = "Breadth=ANY";
	$query .= "&CancelledCourses=Exclude";
	$query .= "&ClassType=ALL";
	
	$query .= "&CourseCodes=";
	$query .= "&CourseNum=";
	if($range)
	{
		$query .= urlencode($range);
	}
	$query .= "&CourseTitle=";
	$query .= "&Days=";
	$query .= "&Dept=".urlencode(str_replace("+"," ",$dept));
	$query .= "&Division=ANY";
	$query .= "&EndTime=";
	$query .= "&FontSize=100";
	$query .= "&FullCourses=ANY";
	$query .= "&InstrName=";
	$query .= "&MaxCap=";
	$query .= "&StartTime=";
	$query .= "&Submit=Display+Text+Results";
	$query .= "&Units=";
	$query .= "&YearTerm=".$term;
	
	return sendRequest($query);
}

function sendRequest($query=false)
{
	$fp = fsockopen("websoc.reg.uci.edu", 80, $errno, $errstr, 30);
	if (!$fp) 
	{
		echo "ERROR";
	}
	else 
	{
		if($query)
		{
			$out = "POST /perl/WebSoc HTTP/1.1\r\n";
			$out .= "Host: websoc.reg.uci.edu\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "Content-Length: ".strlen($query)."\r\n";
			$out .= "Connection: close\r\n\r\n";
			$out .= $query;
		}
		else
		{
			$out = "GET /perl/WebSoc HTTP/1.1\r\n";
			$out .= "Host: websoc.reg.uci.edu\r\n";
			$out .= "Connection: close\r\n\r\n";
		}
	    fwrite($fp, $out);
	    while (!feof($fp)) 
	    {
	        $data .= fgets($fp, 8192);
	    }
	    fclose($fp);
	    
		$response = split("\r\n\r\n",$data);
		$header = $response[0];
		$responsecontent = $response[1];
		if(!(strpos($header,"Transfer-Encoding: chunked")===false))
		{
			$aux = split("\r\n",$responsecontent);
			for($i=0;$i<count($aux);$i++)
			{
				if( $i==0 || ($i%2==0) )
				{
					$aux[$i] = "";
				}
				$responsecontent = implode("",$aux);
			}
		}//if
		return chop($responsecontent);
	}
}



?>
