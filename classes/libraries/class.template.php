<?php
/*****************************************************************
* UCStats - A stats generation program for Half-Life games.
* Copyright (c) 2003 Mike Cao <mike@mikecao.com>
*****************************************************************/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*/

/*****************************************************************
* Document class
*****************************************************************/

class Template 
{

	private $document 	= array();
	private $template 	= array();
	private $data     	= array();
    private $root 		= "";
    private $globalRoot = "";
    private $tempdir	= "";

    public function __construct($root=false)
    {
        $this->root = $root ? $root : getcwd()."/templates/";
		$this->document = array();
		$this->data = array();
		$this->tempdir = dirname($_SERVER["SCRIPT_FILENAME"])."/templates/";
    }
    
    public function setTempDir($path)
    {
    	$this->tempdir = $path;
    }

	public function setRoot($path)
	{
    	$this->root = $path;
	}	
    public function setGlobalRoot($path)
    {
        $this->globalRoot = $path;
    }

	private function getPath($file)
	{
		$file = str_replace(":","/",$file);
    	if( substr($file,0,1)=="/" )
    	{
    		return $this->root.$file;
    	}
    	else 
    	{
    		return $this->tempdir.$file;
    	}
	}	

	// Creates the document from the set template
	public function createDocument() 
	{
		if (sizeof($this->template)) 
		{
			$this->parseTemplate($this->template);
			return 1;
		}
		else 
		{
			$this->document[] .= 'L_ERR_TEMPLATE'.'<br>';
			return 0;
		}
	}
	
	// Parses a template
	private function parseTemplate($template) 
	{
		$parse_state = true;
		$parse_key = "";
		if (sizeof($template)) 
		{
			// Go through template line by line
			for ($i = 0; $i < sizeof($template); $i++) 
			{
				// Conditional statement found
				if ($parse_state && preg_match("/<!--BEGIN EXISTS data=\"(.*)\"-->/",$template[$i],$args)) 
				{
					list(,$key) = $args;
					
					// Clear tag
					$template[$i] = '';

					// Check if data exists or has records in document
					if ( 	$this->getDocumentData($key)==false 
						 || is_null($this->getDocumentData($key)) 
						 || !sizeof($this->getDocumentData($key)) 
					   ) 
					{
						$parse_state = false;
						$parse_key = "<!--END EXISTS data=\"$key\"-->";
					}
				}
				// Conditional statement found
				if ($parse_state && preg_match("/<!--COMMENT/",$template[$i],$args)) 
				{
					$template[$i] = '';
					$parse_state = false;
					$parse_key = "-->";
				}
				// Loop found
				if (	$parse_state 
					&& preg_match("/<!--BEGIN LOOP data=\"(.*)\"-->/",$template[$i],$args)
				   ) 
				{
					list(,$key) = $args;
					
					// Clear tag
					$template[$i] = '';

					// Start one line after
					$loop_start = $i+1;
					
					// Get specified data set
					$dataset = $this->getDocumentData($key);
					
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
							while( !preg_match("/<!--END LOOP-->/",$template[$i]) ) 
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
					// Turn off parsing
					else 
					{
						$parse_state = false;
						$parse_key = "<!--END LOOP-->";
					}
				}
				// If allowed to parse
				if ($parse_state) 
				{
					if (!preg_match("/<!--END/",$template[$i])) 
					{
						$this->document[] = $this->parseCode($template[$i]);
					}
				}
				// Look for key end statement
				else 
				{
					if (preg_match("/$parse_key/",$template[$i])) 
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
		if (sizeof($tokens)) 
		{
			for ($i = 0; $i < sizeof($tokens); $i++) 
			{
				$str = str_replace('{'.$i.'}',$tokens[$i],$str);
			}
		}
		return $str;
	}
	
	// Sets the document template
	public function setTemplate($file) 
	{
		if (file_exists($this->getPath($file))) 
		{
			$this->template = file($this->getPath($file));
			return 1;
		}
		else 
		{
			$this->document[] .= 'ERR_FILE_NOT_FOUND: '.$this->getPath($file);
			return 0;
		}
	}
	
	// Adds to the document from a file
	public function addFile($file) 
	{
		if (file_exists( $this->getPath($file))) 
		{
			$fp = fopen( $this->getPath($file), "r");
			$this->document[] = $this->parseCode(fread($fp, filesize( $this->getPath($file))));
			fclose($fp);
			return 1;
		}
		$this->document[] .= $this->get_token_string('L_ERR_FILE_NOT_FOUND', array($this->getPath($file)));
		return 0;
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
		chmod($file,0660);
	}
	
	// Returns document as a string
	public function publishToString() 
	{
		$text = implode("",$this->document);
		
		$text = str_replace("\r","",$text);
		
		return $text;
	}

    public function toBrowser($kill=true)
    {
		global $DB, $BREADCRUMB;
		
		// setup the breadcrumb
		$this->set("breadcrumb",$BREADCRUMB->toHTML() );
		
		// build the document
		$this->createDocument();
		
		$data = $this->publishToString();	
		echo $data;
		die();
    }
	
    
	public function print_gzipped_page() 
	{
		if( headers_sent() )
		{
			$encoding = false;
		}
		elseif( strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false )
		{
			$encoding = 'x-gzip';
		}
		elseif( strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== false )
		{
			$encoding = 'gzip';
		}
		else
		{
			$encoding = false;
		}
		
		if( $encoding )
		{
			$contents = ob_get_contents();
			ob_end_clean();
			header('Content-Encoding: '.$encoding);
			print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
			$size = strlen($contents);
			$contents = gzcompress($contents, 9);
			$contents = substr($contents, 0, $size);
			print($contents);
			exit();
		}
		else
		{
			ob_end_flush();
			exit();
		}
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
	public function setDocumentData($key,$data) 
	{
		$this->data[$key] = $data;
	}
    public function set($k,$d){ $this->setDocumentData($k,$d); }//alias

	// Gets data from document
	private function getDocumentData($key) 
	{
		if (isset($this->data[$key])) 
		{
			return $this->data[$key];
		}
	}
	
	// Destroys document data
	public function unsetDocumentData($key) 
	{
		unset($this->data[$key]);
	}
    public function clear($k) { $this->unsetDocumentData($k); }
	
	// Parse PHP code from string
	private function parseCode($str) 
	{
		preg_match_all("/{(.*?)}/",$str,$args);
		foreach ($args[1] as $code) 
		{
			// Document data
			if (strlen($code) && $code{0}=='$') 
			{
				$str = str_replace('{'."$code".'}',$this->getDocumentData(substr($code,1)),$str);
			}
			// Functions and global variables
			else if (substr($code,0,5) == 'exec:') 
			{
				$code = substr($code,5);
				if (strlen($code) && $code{0} == '$') eval("global $code;");
				if (strlen($code) && $code{0} != '$') 
				{
					if (!defined($code) && !preg_match("/^(.*)\((.*)\)$/",$code)) $code = "null";
				}
				eval("\$replace = $code;");
				$str = str_replace('{'."exec:$code".'}',$this->parseCode($replace),$str);
			}
			else if (substr($code,0,8) == 'include:') 
			{
				$code = substr($code,8);
				eval("\$file = $code;");
				if (file_exists( $this->getPath($file) )) 
				{
					$this->parseTemplate(file( $this->getPath($file)));
				}
				$str = str_replace('{'."include:$code".'}','',$str);
			}
		}
		return $str;
	}
	
	
	
}
?>
