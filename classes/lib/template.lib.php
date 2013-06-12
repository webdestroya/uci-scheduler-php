<?php

/**
 * Builds the HTML templates
 * 
 * @copyright Copyright (c) 2003 Mike Cao <mike@mikecao.com>
 * @package libraries
 * @subpackage Template
 */
class Template 
{

	private $document		= array();
	private $template		= array();
	private $data			= array();

	private $root			= "";
	private $globalRoot		= "";
	private $tempdir		= "";


	public function __construct($path)
	{
		$this->root = getcwd()."/templates/";
		
		$this->document = array();
		$this->data = array();
		
		$this->tempdir = dirname($_SERVER["SCRIPT_FILENAME"])."/templates/";

		$this->globalRoot	= $path;
	}
	
	private function getPath($file)
	{
		if( substr($file,0,1)=="/" )
		{
			return $this->globalRoot.$file;
		}
		else 
		{
			return $this->root.$file;
		}
	}	

	// Creates the document from the set template
	public function createDocument() 
	{
		if(sizeof($this->template)) 
		{
			$this->parseTemplate($this->template);
			return 1;
		}
		else 
		{
			throw new TemplateEmptyException("");
		}
	}
	
	// Parses a template
	private function parseTemplate($template) 
	{
		$parse_state = true;
		$parse_key = "";
		if(sizeof($template)) 
		{
			// Go through template line by line
			for($i = 0; $i < sizeof($template); $i++) 
			{
				// Conditional statement found
				if($parse_state && preg_match("/<!--BEGIN EXISTS data=\"(.*)\"-->/",$template[$i],$args)) 
				{
					list(,$key) = $args;
					
					// Clear tag
					$template[$i] = '';

					// Check if data exists or has records in document
					if( $this->get($key)==false || is_null($this->get($key)) || !sizeof($this->get($key)) ) 
					{
						$parse_state = false;
						$parse_key = "<!--END EXISTS data=\"$key\"-->";
					}
				}

				// Comment block
				/*if($parse_state && preg_match("/<!--BEGIN COMMENT-->/",$template[$i],$args)) 
				{
					$template[$i] = '';
					$parse_state = false;
					$parse_key = "<!--END COMMENT-->";
				}*/
				
				// Loop found
				if($parse_state && preg_match("/<!--BEGIN LOOP data=\"(.*)\"-->/",$template[$i],$args) ) 
				{
					list(,$key) = $args;
					
					// Clear tag
					$template[$i] = '';

					// Start one line after
					$loop_start = $i+1;
					
					// Get specified data set
					$dataset = $this->get($key);
					
					// Loop start
					$j = 0;
					
					// Data exists in document
					if(!is_null($dataset) && sizeof($dataset)) 
					{
						// Parse individual records in data set
						foreach($dataset as $datakey => $dataval) 
						{
							foreach($this->data[$key][$datakey] as $k => $v) 
							{
								$this->data[$k] = $v;
							}
							
							// Parse records until end found
							while( !preg_match("/<!--END LOOP data=\"$key\"-->/",$template[$i]) ) 
							{
								$this->document[] = $this->parseCode($template[$i]);
								if ($i++ > sizeof($template)) 
								{
									break;
								}
							}

							// Restart loop
							if($j++ < sizeof($dataset)-1)
							{
								$i = $loop_start;
							}
						}
					}
					else 
					{
						// Turn off parsing
						$parse_state = false;
						$parse_key = "<!--END LOOP data=\"$key\"-->";
					}
				}

				// If allowed to parse
				if($parse_state) 
				{
					if(!preg_match("/<!--END/",$template[$i])) 
					{
						$this->document[] = $this->parseCode($template[$i]);
					}
				}
				else 
				{
					// Look for key end statement
					if(preg_match("/$parse_key/",$template[$i])) 
					{
						$parse_state = true;
					}
				}
			}
		}
	}
	
	// Gets a string based on tokens
	private function get_token_string($str,$tokens) 
	{
		if(sizeof($tokens)) 
		{
			for($i=0; $i<sizeof($tokens); $i++) 
			{
				$str = str_replace('{'.$i.'}',$tokens[$i],$str);
			}
		}
		return $str;
	}
	
	// Sets the document template
	public function setTemplate($file) 
	{
		$file = $this->getPath($file);
		if(file_exists($file)) 
		{
			$this->template = file($file);
			return 1;
		}
		else 
		{
			throw new TemplateFileNotFoundException($file);
		}
	}
	
	// Adds to the document from a file
	public function addFile($file) 
	{
		$file = $this->getPath();
		if( file_exists($file) ) 
		{
			$fp = fopen( $file, "r");
			$this->document[] = $this->parseCode(fread($fp, filesize( $file)));
			fclose($fp);
			return 1;
		}
		throw new TemplateFileNotFoundException($file);
	}
	
	// Adds to the document from a string
	public function addString($str) 
	{
		$this->document[] = $this->parseCode($str);
	}
	
	// Prints this document to the browser
	public function publishToBrowser()
	{
		echo $this->publishToString();
	}
	
	// Writes this document to a file
	public function publishToFile($file) 
	{
		$fp = fopen($file, "w+");
		flock($fp,2);
		fwrite($fp, $this->publishToString());
		fclose($fp);
		chmod($file,0777);
	}
	
	// Returns document as a string
	public function publishToString() 
	{
		$text = implode("",$this->document);		
		$text = str_replace("\r","",$text);
		return $text;
	}

	public function toBrowser($do_not_set_next_page=false)
	{
		global $DB;
		
		if(!$do_not_set_next_page)
		{
			$_SESSION['lastpage'] = $_SERVER['REQUEST_URI'];
		}
	
		if(!$this->template)
		{
			$file = basename($_SERVER['SCRIPT_FILENAME']);
			$file = str_replace(".php",".tpl",$file);
			$this->setTemplate($file);
		}
		
		if($_GET['debug']=="1")
		{
			$this->set("mysqldebug",$DB->getDebug());
		}
		
		
		// build the document
		$this->createDocument();
		

		echo $this->publishToString();
		die();
	}
	
	// Clears the document
	public function clearDocument() 
	{
		$this->document = array();
	}
	
	// Clears the document template
	public function clearTemplate() 
	{
		$this->template = array();
	}
	
	// Sets data for document
	public function set($key,$data) 
	{
		$this->data[$key] = $data;
	}
	// Gets data from document
	private function get($key) 
	{
		if(isset($this->data[$key])) 
		{
			return $this->data[$key];
		}
	}
	
	// Destroys document data
	public function clear($k)
	{
		unset($this->data[$k]);
	}
	
	// Parse PHP code from string
	private function parseCode($str) 
	{
		preg_match_all("/{(.*?)}/",$str,$args);
		foreach($args[1] as $code) 
		{
			// Document data
			if(strlen($code) && $code{0}=='$') 
			{
				$str = str_replace('{'."$code".'}',$this->get(substr($code,1)),$str);
			}
			// Functions and global variables
			elseif(substr($code,0,5) == 'exec:') 
			{
				$code = substr($code,5);
				if(strlen($code) && $code{0} == '$')
				{
					eval("global $code;");
				}
				if(strlen($code) && $code{0} != '$') 
				{
					if(!defined($code) && !preg_match("/^(.*)\((.*)\)$/",$code))
					{
						$code = "null";
					}
				}
				eval("\$replace = $code;");
				$str = str_replace('{'."exec:$code".'}',$this->parseCode($replace),$str);
			}
			elseif(substr($code,0,8) == 'include:') 
			{
				$code = substr($code,8);
				eval("\$file = $code;");
				if(file_exists( $this->getPath($file) )) 
				{
					$this->parseTemplate(file( $this->getPath($file)));
				}
				$str = str_replace('{'."include:$code".'}','',$str);
			}
		}
		return $str;
	}
}//Template


class TemplateFileNotFoundException extends Exception {}
class TemplateEmptyException extends Exception {}


?>
