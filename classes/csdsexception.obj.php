<?php

class CSDSException extends Exception 
{
	public function __construct($message, $code = 0)
	{
		// some code
	
		// make sure everything is assigned properly
		parent::__construct($message, $code);
	}
	
	// custom string representation of object
	public function __toString()
	{
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}


//THIS SHOULD BE IN includes.php

/*
function exception_handler($exception) 
{
	show_error_page($exception->getMessage());
}
set_exception_handler('exception_handler');
*/


?>
