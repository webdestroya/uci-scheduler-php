<?php

 /**
  * Functions class
  * @package ANHSPublicSite
  * @author Matt Mecham <matt@invisionpower.com>
  * @link http://www.invisionboard.com
  * @version 1.0.0 - Invision Power Board v1.2
  * @copyright (c) 2001 - 2003 Invision Power Services
  * @filesource
  */ 
class FUNC 
{
	
	public static function make_mysql_date($date=false)
	{//2006-07-06 13:38:48
		$date = $date ? $date : time();
		return date("Y-m-d H:i:s",$date);
	}
	
	public static function make_mysql_datestamp($date=false)
	{
		$date = $date ? $date : time();
		return date("Y-m-d",$date);
	}

	public static function html_activate_links($str) 
	{
		$str = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_+.~#?&//=]+)', '<a href="\1" target="_blank">\1</a>', $str);
		$str = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_+.~#?&//=]+)', '\1<a href="http://\2" target="_blank">\2</a>', $str);
		$str = eregi_replace('([_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3})','<a href="mailto:\1">\1</a>', $str);
		return $str;
	}
	
	/**
	 * Returns a scaled image
	 * @param array $arg (max_width, max_height, cur_width, cur_height)
	 * @returns array (img_width,img_height)
	 */
	public static function scale_image($arg)
	{
		// max_width, max_height, cur_width, cur_height
		
		$ret = array(
					  'img_width'  => $arg['cur_width'],
					  'img_height' => $arg['cur_height']
					);
		
		if ( $arg['cur_width'] > $arg['max_width'] )
		{
			$ret['img_width']  = $arg['max_width'];
			$ret['img_height'] = ceil( ( $arg['cur_height'] * ( ( $arg['max_width'] * 100 ) / $arg['cur_width'] ) ) / 100 );
			$arg['cur_height'] = $ret['img_height'];
			$arg['cur_width']  = $ret['img_width'];
		}
		
		if ( $arg['cur_height'] > $arg['max_height'] )
		{
			$ret['img_height']  = $arg['max_height'];
			$ret['img_width']   = ceil( ( $arg['cur_width'] * ( ( $arg['max_height'] * 100 ) / $arg['cur_height'] ) ) / 100 );
		}
		
	
		return $ret;
	
	}
	
	//TODO: FINISH THIS CHECKER
	public static function expectVars()
	{
		$args = func_get_args();
		
		$missing = false;
		// 0 , 1, 2, 3 | 1
		// <r/o>, <n/s>, <G/P>, [#] | <field>
		// req/opt, num/str, get/pst, opt:length | fieldname
		
		$vars = array(
		'G'=>$_GET,
		'P'=>$_POST,
		);
		
		foreach($args as $arg)
		{
			$part = explode("|",$arg);
			
			$opts = explode(",",$part[0]);
			
			if($opts[0]=="r")
			{
				//required
				
				
				// checking the length
				if( $opts[3] > 0 )
				{
					if( strlen($vars[$opts[2]])!=$opts[3] )
					{
						$missing = true;
					}
				}
				
			}
			else 
			{
				// optional
				
			}
			
			
		}
		
		if($missing)
		{
			header("Location: /error/error.php");
			exit();
		}
	}
	
	public static function numberOnly($data)
	{
		$data = str_replace(array("/","\\","'",",",":",";",".","*"),"",$data);
		$data = trim($data);
		return $data;
	}
	
	public static function encrypt($data)
	{
		return blowfish_encrypt($data,BLOWFISH_SECRET);
	}
	public static function decrypt($data)
	{
		return blowfish_decrypt($data,BLOWFISH_SECRET);
	}
	
	
	/**
	 * Shows a GIF image of a number
	 * @param int $this_number the number of the image
	 * @returns image of the number
	 */
	public static function show_gif_img($this_number="")
	{
		$numbers = array( 0 => 'R0lGODlhCAANAJEAAAAAAP////4BAgAAACH5BAQUAP8ALAAAAAAIAA0AAAIUDH5hiKsOnmqSPjtT1ZdnnjCUqBQAOw==',
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
		exit();
		
		
	}
	
	/**
	 * Creates a GC Security Image
	 * @param string $content the content in the image
	 * @returns image
	 */
	public static function show_gd_img($content="",$use_ttf=0)
	{
		flush();
		
		@header("Content-Type: image/jpeg");
		
		if ( $use_ttf != 1 )
		{
			$font_style = 5;
			$no_chars   = strlen($content);
			
			$charheight = imagefontheight($font_style);
			$charwidth  = imagefontwidth($font_style);
			$strwidth   = $charwidth * intval($no_chars);
			$strheight  = $charheight;
			
			$imgwidth   = $strwidth  + 15;
			$imgheight  = $strheight + 15;
			$img_c_x    = $imgwidth  / 2;
			$img_c_y    = $imgheight / 2;
			
			$im       = imagecreate($imgwidth, $imgheight);
			$text_col = imagecolorallocate($im, 0, 0, 0);
			$back_col = imagecolorallocate($im, 200,200,200);
			
			imagefilledrectangle($im, 0, 0, $imgwidth, $imgheight, $text_col);
			imagefilledrectangle($im, 3, 3, $imgwidth - 4, $imgheight - 4, $back_col);
			
			$draw_pos_x = $img_c_x - ($strwidth  / 2) + 1;
			$draw_pos_y = $img_c_y - ($strheight / 2) + 1;
			
			imagestring($im, $font_style, $draw_pos_x, $draw_pos_y, $content, $text_col);
		
		}
		else
		{
			$image_x = 250;
			$image_y = 70;
			
			$im = imagecreate($image_x,$image_y);
			
			$white    = imagecolorallocate($im, 255, 255, 255);
			$black    = imagecolorallocate($im, 0, 0, 0);
			$grey     = imagecolorallocate($im, 200, 200, 200 );
			
			$no_x_lines = ($image_x - 1) / 5;
			
			for ( $i = 0; $i <= $no_x_lines; $i++ )
			{
				// X lines
				
				imageline( $im, $i * $no_x_lines, 0, $i * $no_x_lines, $image_y, $grey );
				
				// Diag lines
				
				imageline( $im, $i * $no_x_lines, 0, ($i * $no_x_lines)+$no_x_lines, $image_y, $grey );
			}
			
			$no_y_lines = ($image_y - 1) / 5;
			
			for ( $i = 0; $i <= $no_y_lines; $i++ )
			{
				imageline( $im, 0, $i * $no_y_lines, $image_x, $i * $no_y_lines, $grey );
			}
			
			$font = WEBSITE_ROOT."includes/progbot.ttf";
		
			$text_bbox = imagettfbbox(20, 0, $font, $content);
			
			$sx = ($image_x - ($text_bbox[2] - $text_bbox[0])) / 2; 
			$sy = ($image_y - ($text_bbox[1] - $text_bbox[7])) / 2; 
			$sy -= $text_bbox[7];
			
			imagettftext($im, 20, 0, $sx, $sy, $black, $font, $content);
		}
		
		
		imagejpeg($im);
		imagedestroy($im);
		
		exit();
		
	}
	
	/**
	 * Convert newlines to <br /> nl2br is buggy with <br /> on early PHP builds
	 * @param string $t the text to convert
	 * @returns string
	 */
	public static function my_nl2br($t="")
	{
		return str_replace( "\n", "<br />", $t );
	}
	
	/**
	 * Convert <br /> to newlines
	 * @param string $t the text to convert
	 * @returns string
	 */
	public static function my_br2nl($t="")
	{
		$t = str_replace( "<br />", "\n", $t );
		$t = str_replace( "<br>"  , "\n", $t );
		
		return $t;
	}
		
	/**
	 * Converts byte file sizes into their higher eqivalents
	 * @param int $filesize the filesize in bytes
	 * @returns string
	 */
	public static function size($filesize)
	{
		if ($filesize >= 1073741824)	{	$filesize = round($filesize / 1073741824 * 100) / 100 . " GB";	}
		else if ($filesize >= 1048576)	{	$filesize = round($filesize / 1048576 * 100) / 100 . " MB";		}
		else if ($filesize >= 1024)		{	$filesize = round($filesize / 1024 * 100) / 100 . " KB";		}
		else							{	$filesize = $filesize . " B";									}
		return $filesize;
	}
	
	/**
	 * Creates a META refresh tag
	 * @param string $url the url to send them to
	 * @param int $time the wait time, seconds
	 * @returns echos text
	 */
	public static function refresh($url, $time='0')
	{
		$url = str_replace( "&amp;", "&", $url );
		@flush();
		echo("<html><head><meta http-equiv='refresh' content='$time; url=$url'></head><body></body></html>");
		exit();
	}
	
	/**
	 * Makes a random 8 character password
	 * @returns string
	 */
	public static function make_password()
	{
		$pass = "";
		$chars = array(
			"1","2","3","4","5","6","7","8","9",
			"a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J",
			"k","K","l","L","m","M","n","N","p","P","q","Q","r","R","s","S","t","T","u","U",
			"v","V","w","W","x","X","y","Y","z","Z");
	
		$count = count($chars) - 1;
	
		srand((double)microtime()*1000000);

		for($i = 0; $i < 8; $i++)
		{
			$pass .= $chars[rand(0, $count)];
		}
	
		return($pass);
	}
	
	/**
	 * Cleans up email addresses, or says they are fake
	 * @param string $email the email address
	 * @returns string
	 */
	public static function clean_email($email = "") {

		$email = trim($email);
		
		$email = str_replace( " ", "", $email );
		
    	$email = preg_replace( "#[\;\#\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/]#", "", $email );
    	
    	if ( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $email) )
    	{
    		return $email;
    	}
    	else
    	{
    		return FALSE;
    	}
	}
    
   
    
    /**
	 * Cleans the keys
	 * @param string $key the key to clean
	 * @returns string
	 */
    public static function clean_key($key) {
    
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
	 * @returns string
	 */
    public static function clean_value($val)
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
    
    
	/**
	 * Removes IBF Tags
	 * @param string $text the data
	 * @returns string
	 */
    public static function remove_tags($text="")
    {
    	// Removes < BOARD TAGS > from posted forms
    	$text = preg_replace( "/(<|&lt;)% (BOARD HEADER|CSS|JAVASCRIPT|TITLE|BOARD|STATS|GENERATOR|COPYRIGHT|NAVIGATION) %(>|&gt;)/i", "&#60;% \\2 %&#62;", $text );
    	//$text = str_replace( "<%", "&#60;%", $text );
    	return $text;
    }
    
	/**
	 * Is the text a number
	 * @param string $number the number to test
	 * @returns string
	 */
    public static function is_number($number="")
    {
    	if ($number == "") return -1;
    	
    	if ( preg_match( "/^([0-9\.]+)$/", $number ) )
    	{
    		return $number;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    public static function mimeToEnglish($mime)
    {
    	$data = "Unknown File Type";
    	
    	switch(strtolower($mime))
    	{
    		case "txt": case "log": case "rtf":
    		case "text/plain":
    		case "text/richtext";
    			$data = "Text Document";
    			break;
    		case "csv":
    			$data = "Comma Separated File";
    			break;
    		case "sql":
    			$data = "SQL Query Script";
    			break;	
    		case "html": case "htm":
    		case "text/html":
    			$data = "HTML Page";
    			break;
    		
    		case "vbs": case "pif":
    			$data = "<span class='red'>DANGEROUS FILE</span>";
    			break;
    			
    		case "java": case "class":
    			$data = "Java Source/Class File";
    			break;
    			
    		case "pdf": case "application/pdf":
    			$data = "Adobe PDF";
    			break;
    		
    		case "rar":
    		case "application/zip": case "zip":
    			$data = "ZIP Archive";
    			break;
    		
    		case "application/msword": case "doc":
    			$data = "Word Document";
    			break;
    			
    		case "application/vnd.ms-powerpoint": case "ppt":
    			$data = "Powerpoint";
    			break;
    			
    		case "application/vnd.ms-excel": case "xls":
    			$data = "Excel Document";
    			break;
    			
    		case "image/jpeg": case "jpeg":
    		case "image/png": case "png":
    		case "image/gif": case "gif":
    		case "image/jpg": case "jpg":
    		case "image/bmp": case "bmp":
    			$data = "Image";
    			break;
    		
    		case "video/x-ms-asf":
    		case "asf": case "avi": case "mpeg": case "mpg": case "wmv": case "asx":
    			$data = "Video File";
    			break;
    			
    		case "audio/mpeg": case "mp3":
    		case "wav": case "m4a": case "aac": case "aiff": case "ogg": case "wma":
    			$data = "Audio File";
    			break;
    		
    		case "application/octet-stream": case "exe":
    		case "application/x-msdownload": case "dll":
    		case "application/x-msdos-program": case "com":
    		case "bat":
    			$data = "Executable";
    			break;
    	}
    	
    	return $data;
    }
    
    public static function extToMIME($ext)
    {
    	$mime = "application/octet-stream";
    	switch($ext)
    	{
    		//TODO: WE NEED TO ADD ALL EXTENSIONS
    		case "txt":
    			//$mime = "text/plain";
    			break;
    		
    	}
    	return $mime;
    }
    
    public static function extToImage($ext)
    {
    	/*
    	audio-x-generic.gif
    	video-x-generic.gif
    	text-x-generic.gif
    	text-html.gif
    	text-x-script.gif
    	x-office-document.gif
    	x-office-document-template.gif
    	x-office-drawing.gif
    	x-office-drawing-template.gif
    	x-office-presentation.gif
    	x-office-presentation-templ.gif
    	x-office-spreadsheet.gif
    	x-office-spreadsheet-templa.gif
    	package-x-generic.gif
    	image-x-generic.gif
    	*/
    	$img = "text-x-generic-template";
    	switch(strtolower($ext))
    	{
    		case "mp3":	case "wav":	case "m4a":	case "ogg":	case "acc":	case "m4p":	case "wma":
    			$img = "audio-x-generic";
    			break;
    		case "doc":	case "wps":
    			$img = "x-office-document";
    			break;
    		case "xls": case "wsp":
    			$img = "x-office-spreadsheet";
    			break;
    		case "ppt":
    			$img = "x-office-presentation";
    			break;
    		case "jpg":	case "jpeg": case "png": case "gif": case "bmp": case "tiff": case "raw": case "svg":
    			$img = "image-x-generic";
    			break;
    		case "zip":	case "gz": case "tar": case "bz2": case "rar":
    			$img = "package-x-generic";
    			break;
    		case "txt": case "ini":	case "rtf":	case "nfo":
    			$img = "text-plain";
    			break;
    		case "html": case "htm": case "css":
    			$img = "text-html";
    			break;
    		case "mov":	case "avi": case "mpeg": case "mpg": case "wmv": case "flv":
    			$img = "video-x-generic";
    			break;	
    		case "java": case "cpp": case "h": case "class": case "asp": case "php": case "vbs":
    			$img = "text-x-script";
    			break;	
    	}
    	return $img;
    }
    
    public static function ext($name)
    {
    	return trim(strrchr($name,"."),".");
    }
    
    
    public static function makeSmileys($data,$size=16)
    {
    	$data = str_replace(array(":angel:"),
    		'<img src="/img.php/i/'.$size.'/face-angel" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array("T_T",":'("),
    		'<img src="/img.php/i/'.$size.'/face-crying" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(":devil:"),
    		'<img src="/img.php/i/'.$size.'/face-devil-grin" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array("8-)"),
    		'<img src="/img.php/i/'.$size.'/face-glasses" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(":D",":-D"),
    		'<img src="/img.php/i/'.$size.'/face-grin" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(":-*",":*"),
    		'<img src="/img.php/i/'.$size.'/face-kiss" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(":|",":-|"),
    		'<img src="/img.php/i/'.$size.'/face-plain" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(":(",":-("),
    		'<img src="/img.php/i/'.$size.'/face-sad" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(":)",":-)"),
    		'<img src="/img.php/i/'.$size.'/face-smile" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(":-0",":-O"),
    		'<img src="/img.php/i/'.$size.'/face-surprise" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	$data = str_replace(array(";)",";-)"),
    		'<img src="/img.php/i/'.$size.'/face-wink" width="'.$size.'" height="'.$size.'" />',
    		$data);
    	return $data;
    }
    
    
    public static function extToMIMEImage($ext)
    {
    	$img = "application";
    	switch(strtolower($ext))
    	{
    		case "mp3": case "wav": case "m4a": case "ogg": case "acc": case "m4p": case "wma":
    			$img = "audio";
    			break;
    		case "doc": case "dot": case "wps":
    			$img = "application-msword";
    			break;
    		case "xls": case "wsp":
    			$img = "x-office-spreadsheet";
    			break;
    		case "ppt":
    			$img = "x-office-presentation";
    			break;
    		case "pdf":
    			$img = "application-pdf";
    			break;
    		case "jpg": case "jpeg": case "png": case "gif": case "bmp": case "tiff": case "raw": case "svg":
    			$img = "image";
    			break;
    		case "zip":	case "gz": case "tar": case "bz2": case "rar": case "7z":
    			$img = "application-zip";
    			break;
    		case "txt": case "ini": case "rtf": case "nfo": case "csv":
    			$img = "text-plain";
    			break;
    		case "html": case "htm":
    			$img = "text-html";
    			break;
    		case "css":
    			$img = "text-css";
    			break;
    		case "mov": case "avi": case "mpeg": case "mpg": case "wmv": case "flv": case "mkv":
    			$img = "video";
    			break;	
    		case "java": case "cpp": case "sh": case "h": case "asp": case "php": case "sql":
    			$img = "application-x-shellscript";
    			break;
    		case "exe": case "dll": case "bat": case "com": case "vbs": case "class":
    			$img = "application-x-executable";
    			break;
    	}
    	return $img;
    }
    
    
    public static function cleanFilename($filename,$level2=true)
    {
    	$ext = trim(strrchr($filename,"."),".");
    	$filename = str_replace($ext,"",$filename);
    	$filename = str_replace(array('"',"&","*","[","]","%","#","@","!","$","<",">","^","(",")","=","+",";",".",",","~"),"",$filename);
    	if($level2)
    	{
    		$filename = str_replace(array(" ","-",),"",$filename);
    	}
    	$filename = $filename.".".$ext;
    	return $filename;
    }

    public static function formdate_to_unix($array,$type="datetime")
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
    
    
    public static function doClickTracking()
    {
    	global $DB, $SESSION;
    	if(CLICK_TRACKING)
    	{
    		$insert = $DB->compile_db_insert_string(array(
    		'username'=>$SESSION->getUsername(),
    		'date'=>FUNC::make_mysql_date(),
    		'request'=>substr($_SERVER['REQUEST_URI'],0,255),
    		'ip_address'=>$_SERVER['REMOTE_ADDR'],
    		//'useragent'=>$_SERVER['HTTP_USER_AGENT'],
    		));
    		$DB->query( "INSERT INTO tbl_click_tracking (".$insert['FIELD_NAMES'].") VALUES (".$insert['FIELD_VALUES'].")" );
    	}
    }
    
    
    public static function makeInfoBoxes($text)
    {
    	
    	$text = str_replace(array("[{[","]}]"),array('<nowiki><div class="mb_wrapper"><div class="mb_info"><b>Note:</b></nowiki>',"<nowiki></div></div></nowiki>"),$text);
    	
    	
    	
    	return $text;
    }
    public static function undoInfoBoxes($text)
    {
    	$text = str_replace(array('<nowiki><div class="mb_wrapper"><div class="mb_info"><b>Note:</b></nowiki>',"<nowiki></div></div></nowiki>"),array("[{[","]}]"),$text);
    	
    	
    	
    	return $text;
    }
    
    // Pass the file pointer
    public static function parseCSVFile($filepath,$struct=false,$delim=",")
    {
		$data = array();
		
		$struct[] = "!INVALID!";
		$struct[] = "!INVALID!";
		$struct[] = "!INVALID!";
		$struct[] = "!INVALID!";
		$struct[] = "!INVALID!";
		$struct[] = "!INVALID!";
		
		$row = 0;
		$fp = fopen($filepath,"r");
		while (($line = fgetcsv($fp, 1000, $delim,'"')) !== FALSE) 
		{
			if( is_array($struct) )
			{
				$num = count($line);
				for ($c=0; $c < $num; $c++) 
				{
					$data[$row][ $struct[$c] ] = $line[$c];
				}
			}
			else 
			{
				$data[$row] = $line;
			}
			$row++;
		}
		// close it
		fclose($fp);
		
		return $data;
    }
    
    
    
    public static function getMP3Info($file)
    {
    	$data = array();
    	
    	
		if (!($f = fopen($file, 'rb')) ) 
		{
		    return false;
		}
	
		$data['filesize'] = filesize($file);
	
		do {
		    while (fread($f,1) != Chr(255)) { // Find the first frame
			if (feof($f)) {
			    return false;
			}
		    }
		    fseek($f, ftell($f) - 1); // back up one byte
	
		    $frameoffset = ftell($f);
	
		    $r = fread($f, 4);
		    // Binary to Hex to a binary sting. ugly but best I can think of.
		    $bits = unpack('H*bits', $r);
		    $bits =  base_convert($bits['bits'],16,2);
		} 
		while (!$bits[8] and !$bits[9] and !$bits[10]); // 1st 8 bits true from the while
	
		$data['frameoffset'] = $frameoffset;
	
		fclose($f);
	
		if ($bits[11] == 0) {
		    $data['mpeg_ver'] = "2.5";
		    $bitrates = array(
			    '1' => array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
			    '2' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
			    '3' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
				     );
		} else if ($bits[12] == 0) {
		    $data['mpeg_ver'] = "2";
		    $bitrates = array(
			    '1' => array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
			    '2' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
			    '3' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
				     );
		} else {
		    $data['mpeg_ver'] = "1";
		    $bitrates = array(
			    '1' => array(0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448, 0),
			    '2' => array(0, 32, 48, 56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 384, 0),
			    '3' => array(0, 32, 40, 48,  56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 0),
				     );
		}
		
	
		$layer = array(
			array(0,3),
			array(2,1),
			      );
		$data['layer'] = $layer[$bits[13]][$bits[14]];
		if ($bits[15] == 0) {
		    $$data['crc'] = true;
		}
	
		$bitrate = 0;
		if ($bits[16] == 1) $bitrate += 8;
		if ($bits[17] == 1) $bitrate += 4;
		if ($bits[18] == 1) $bitrate += 2;
		if ($bits[19] == 1) $bitrate += 1;
		$data['bitrate'] = $bitrates[$data['layer']][$bitrate];
	
		$frequency = array(
			'1' => array(
			    '0' => array(44100, 48000),
			    '1' => array(32000, 0),
				    ),
			'2' => array(
			    '0' => array(22050, 24000),
			    '1' => array(16000, 0),
				    ),
			'2.5' => array(
			    '0' => array(11025, 12000),
			    '1' => array(8000, 0),
				      ),
			  );
		$data['frequency'] = $frequency[$data['mpeg_ver']][$bits[20]][$bits[21]];
	
		$data['padding'] = $bits[22];
		$data['private'] = $bits[23];
	
		$mode = array(
			array('Stereo', 'Joint Stereo'),
			array('Dual Channel', 'Mono'),
			     );
		$data['mode'] = $mode[$bits[24]][$bits[25]];
	
		// XXX: I dunno what the mode extension is for bits 26,27
	
		$data['copyright'] = $bits[28];
		$data['original'] = $bits[29];
	
		$emphasis = array(
			array('none', '50/15ms'),
			array('', 'CCITT j.17'),
				 );
		$data['emphasis'] = $emphasis[$bits[30]][$bits[31]];
	
		if ($data['bitrate'] == 0) {
		    $s = -1;
		} else {
		    $s = ((8*filesize($file))/1000) / $data['bitrate'];        
		}
		$data['length'] = sprintf('%02d:%02d',floor($s/60),floor($s-(floor($s/60)*60)));
		$data['lengths'] = (int)$s;
    	
    	return $data;
    }
    
    
	public static function create_bargraph($value, $maximum, $b=2) 
	{
		$barheight = "16";
		$maximum == 0 ? $barwidth = 0 : $barwidth = round((100  / $maximum) * $value) * $b;
		$red = 90 * $b;
		
		if ($barwidth == 0) 
		{
			return '<img height="'.$barheight.'" alt="" src="/images/icons/bar/bar_left.gif">'
			.'<img height="'.$barheight.'" alt="" src="/images/icons/bar/bar_middle.gif" width="1">'
			.'<img height="'.$barheight.'" alt="" src="/images/icons/bar/bar_right.gif">';
		} 
		elseif ( $barwidth < $red ) 
		{
			return '<img height="'.$barheight.'" alt="" src="/images/icons/bar/bar_left.gif">'
			.'<img height="'.$barheight.'" alt="" src="/images/icons/bar/bar_middle.gif" width="'.$barwidth.'">'
			.'<img height="'.$barheight.'" alt="" src="/images/icons/bar/bar_right.gif">';
		} 
		else 
		{
			return '<img height="'.$barheight.'" alt="" src="/images/icons/bar/redbar_left.gif">'
			.'<img height="'.$barheight.'" alt="" src="/images/icons/bar/redbar_middle.gif" width="'.$barwidth.'">'
			.'<img height="'.$barheight.'" alt="" src="/images/icons/bar/redbar_right.gif">';
		}
	}

	
	public static function viewpage($section)
	{
		global $tpl,$DB,$USER,$BREADCRUMB,$titlestart;
		
		$tpl->setTemplate("/viewpage.tpl");
		$tpl->set("title",$titlestart );
		
		$url = $_SERVER["PATH_INFO"];
		$url = explode("/",trim($url,"/"));
		
		if(FUNC::is_number($url[0]))
		{
			if($USER->isLoggedIn() && $USER->hasPerm( strtoupper($section) ))
			{
				$tpl->set("edit_path","pages/edit.php?id=".$url[0]);
			}
			
			$DB->query( "SELECT * FROM tbl_pages WHERE section='".$section."' AND id='".$url[0]."'" );
			
			if( $DB->get_num_rows() )
			{
				$res = $DB->fetch_row();
				/*$data = $res['text'];
				if($res['type']=="wiki")
				{
					$wiki = Text_Wiki::singleton('Mediawiki');
					$data = $wiki->transform($data, 'Xhtml');
					$data = str_replace(array("</h3>\n<br />","</h3><br />"),"</h3>",$data);
				}*/
				
				$BREADCRUMB->AddCrumb($res['title'],"/".$section."/viewpage.php/".$res['id']);
				$tpl->set("pagetitle", $res['title'] );
				$tpl->set("pagedata", base64_decode($res['text']) );
				
				$file = ltrim($_SERVER["REQUEST_URI"],"/");
				$tpl->set("global_request",$file);
				$tpl->set("global_lastupdate",date("n/d/Y",strtotime($res['date'])));
			}
			else
			{
				$tpl->set("pagetitle", "Page not found" );
				$tpl->set("pagedata","Sorry, but the page you requested could not be loaded.");
			}
		}
		else 
		{
			$tpl->set("pagetitle", "Invalid Page ID" );
			$tpl->set("pagedata","Sorry, but the page ID you requested is invalid.");
		}
	}
	
    
} // end class


?>