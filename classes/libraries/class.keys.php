<?php
/*=================================*/
// Key Cleaner and Parser
/*---------------------------------*/
// Very useful script aswell. Thanks
// to whoever made it.
/*=================================*/
class Cleaner 
{
	public $POST		= array();
	public $GET			= array();
	public $IP			= "";
	
	public function __construct()
	{
		if( is_array($_GET) )
		{
			foreach($_GET as $k=>$v)
			{
				if( is_array($_GET[$k]) )
				{
					foreach($_GET[$k] as $k2=>$v2)
					{
						$this->GET[$k][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$this->GET[$k] = $this->clean_value($v);
				}
			}
		}
		
		if( is_array($_POST) )
		{
			foreach($_POST as $k=>$v)
			{
				//$k = $this->clean_key($k);
				if( is_array($_POST[$k]) )
				{
					foreach($_POST[$k] as $k2=>$v2)
					{
						$this->POST[$k][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$this->POST[$k] = $this->clean_value($v);
				}
			}
		}
		
		// Sort out the accessing IP
		$this->IP = $_SERVER['REMOTE_ADDR'];
								 
		// Make sure we take a valid IP address
		$this->IP = preg_replace( "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $this->IP );
	}
	
	public function clean_key($key) 
	{
		if ($key == "")
		{
			return "";
		}
		$key = preg_replace( "/\.\./"           , ""  , $key );
		$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
		$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
		return $key;
	}
	
	public function clean_value($val) 
	{
		if ($val == "")
		{
			return "";
		}
		$val = str_replace( "&#032;"       , " "             , $val );
		$val = str_replace( "&"            , "&amp;"         , $val );
		$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
		$val = str_replace( "-->"          , "--&#62;"       , $val );
		$val = preg_replace( "/<script/i"  , "&#60;script"   , $val );
		$val = str_replace( ">"            , "&gt;"          , $val );
		$val = str_replace( "<"            , "&lt;"          , $val );
		$val = str_replace( "\""           , "&quot;"        , $val );
		$val = preg_replace( "/\|/"        , "&#124;"        , $val );
		$val = preg_replace( "/\n/"        , "<br>"          , $val ); // Convert literal newlines
		$val = preg_replace( "/\\\$/"      , "&#036;"        , $val );
		$val = preg_replace( "/\r/"        , ""              , $val ); // Remove literal carriage returns
		$val = str_replace( "!"            , "&#33;"         , $val );
		$val = str_replace( "'"            , "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
		$val = stripslashes($val);                                     // Swop PHP added backslashes
		$val = preg_replace( "/\\\/"       , "&#092;"        , $val ); // Swop user inputted backslashes
		return $val;
	}
	
}//endclass


?>