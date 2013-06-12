<?php

function debug($msg)
{
	if(defined('DEBUG'))
	{
		echo $msg."\n";
	}
}

function numberOnly($data)
{
	$data = trim($data);
	if( preg_match("/^[0-9]+$/", $data) )
	{
		return $data;
	}
	else
	{
		show_error_page("Invalid request ID recevied. What are you trying to do?");
		//return false;
	}
}

/*function forceNumber($num,$error="Invalid request ID received. What are you trying to do?")
{
	if(!preg_match("/^[0-9]+$/",$num) )
	{
		show_error_page($error);
	}
}
function encrypt($data)
{
	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($size, MCRYPT_RAND);

	//$data .= chr(3).chr(3).chr(3);
	
	return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, BLOWFISH_SECRET, $data, MCRYPT_MODE_ECB, $iv));
}
function decrypt($data)
{
	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($size, MCRYPT_RAND);
	@$ret = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, BLOWFISH_SECRET, pack("H*",$data), MCRYPT_MODE_ECB, $iv);
	$ret = trim($ret);
	return $ret;
}
*/

/**
 * Shows a GIF image of a number
 * @param int $this_number the number of the image
 * @return image of the number
 */
function show_gif_img($this_number="")
{
    $numbers = array( 
    0 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUDH5hiKsOnmqSPjtT1ZdnnjCUqBQAOw==',
	1 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUjAEWyMqoXIprRkjxtZJWrz3iCBQAOw==',
	2 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUDH5hiKubnpPzRQvoVbvyrDHiWAAAOw==',
	3 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVDH5hiKbaHgRyUZtmlPtlfnnMiGUFADs=',
	4 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVjAN5mLDtjFJMRjpj1Rv6v1SHN0IFADs=',
	5 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUhA+Bpxn/DITL1SRjnps63l1M9RQAOw==',
	6 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVjIEYyWwH3lNyrQTbnVh2Tl3N5wQFADs=',
	7 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUhI9pwbztAAwP1napnFnzbYEYWAAAOw==',
	8 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVDH5hiKubHgSPWXoxVUxC33FZZCkFADs=',
	9 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIVDA6hyJabnnISnsnybXdS73hcZlUFADs=',
	);
    
    flush();
    header("Content-type: image/gif");
    echo base64_decode($numbers[ $this_number ]);
    die();
}
    
/**
 * Converts byte file sizes into their higher eqivalents
 * @param int $filesize the filesize in bytes
 * @return string
 */
function size($filesize)
{
    if ($filesize >= 1073741824)
    {
    	$filesize = round($filesize / 1073741824 * 100) / 100 . " GB";
    }
    else if ($filesize >= 1048576)
    {
    	$filesize = round($filesize / 1048576 * 100) / 100 . " MB";
    }
    else if ($filesize >= 1024)
    {
    	$filesize = round($filesize / 1024 * 100) / 100 . " KB";
    }
    else
    {
    	$filesize = $filesize . " B";
    }
    return $filesize;
}

/**
 * Makes a random 8 character password
 * @return string
 */
function make_password()
{
    $pass = "";
    $chars = array(
        "1","2","3","4","5","6","7","8","9","0",
        "a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
        "k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T",
        "u","U","v","V","w","W","x","X","y","Y","z","Z");

    $count = count($chars) - 1;

    srand((double)microtime()*1000000);

    for($i = 0; $i < 8; $i++)
    {
        $pass .= $chars[rand(0, $count)];
    }

    return $pass;
}

/**
 * Cleans up email addresses, or says they are fake
 * @param string $email the email address
 * @return string
 */
function clean_email($email = "") 
{
    $email = trim($email);   
    $email = str_replace( " ", "", $email );
    $email = preg_replace( "#[\;\#\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/]#", "", $email );
    
    if( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $email) )
    {
        return $email;
    }
    else
    {
        return FALSE;
    }
}

function is_email($email)
{
	if($email==clean_email($email))
	{
		return true;
	}
	else 
	{
		return false;
	}
}

/**
 * Cleans the keys
 * @param string $key the key to clean
 * @return string
 */
function clean_key($key) {

    if ($key == "")
    {
        return "";
    }
    $key = preg_replace( "/\.\./"           , ""  , $key );
    $key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
    $key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
    return $key;
}

/**
 * Cleans up a value
 * @param string $val the data to convert
 * @return string
 */
function clean_value($val)
{
    if ($val == "")
    {
        return "";
    }
    
    $val = str_replace( "&#032;", " ", $val );

    $val = str_replace( "&"            , "&amp;"         , $val );
    $val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
    $val = str_replace( "-->"          , "--&#62;"       , $val );
    $val = preg_replace( "/<script/i"  , "&#60;script"   , $val );
    $val = str_replace( ">"            , "&gt;"          , $val );
    $val = str_replace( "<"            , "&lt;"          , $val );
    $val = str_replace( "\""           , "&quot;"        , $val );
    $val = preg_replace( "/\n/"        , "<br>"          , $val ); // Convert literal newlines
    $val = preg_replace( "/\\\$/"      , "&#036;"        , $val );
    $val = preg_replace( "/\r/"        , ""              , $val ); // Remove literal carriage returns
    $val = str_replace( "!"            , "&#33;"         , $val );
    $val = str_replace( "'"            , "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
    
    $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
    $val = stripslashes($val);
    $val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val ); 
    
    return $val;
}

function activateLinks($data)
{
	$data = str_ireplace("http://www.","www.",$data);
	$data = str_ireplace("www.","http://www.",$data);
	
	
	preg_match_all("/http:\/\/([^\)\( ]+)/i",$data,$match);
    foreach($match[0] as $m)
    {
        $ln = $m;//substr($m,0,30)."...";
        $data = str_ireplace($m,'<a href="'.$m.'" target="_blank">'.$ln.'</a>', $data);
    }

	
	return $data;
}

/**
 * Is the text a number
 * @param string $number the number to test
 * @return string
 */
function is_number($number="")
{
    if($number == "")
    {
    	return false;
    }
    
    if( preg_match( "/^([0-9\.]+)$/", $number ) )
    {
        return true;
    }
    else
    {
        return false;
    }
}

function ext($name)
{
    return trim(strrchr($name,"."),".");
}


function cleanFilename($filename,$level2=true)
{
    $ext = trim(strrchr($filename,"."),".");
    $filename = str_replace($ext,"",$filename);
   
   	$filename = preg_replace("/[^0-9 a-zA-Z_]/","",$filename);
	$filename = str_replace(" ","_",$filename);

	$filename = $filename.".".$ext;
	$filename = trim($filename,".");
    return $filename;
}

function formdate_to_unix($array,$type="datetime")
{
    // m,d,y,h,i,s,a
    $time = 0;
    switch($type)
    {
        case "date":
            $time = mktime(0,0,0,$array['m'],$array['d'],$array['y']);
            break;
        case "datetime":
            $time = mktime(($array['h']+$array['a']),$array['i'],0,$array['m'],$array['d'],$array['y']);
            break;
      }
    return $time;
}

?>
