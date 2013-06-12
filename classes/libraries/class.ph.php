<?php
/***
* PH Class for UC Irvine
* Send any questions to ecarter@uci.edu
*
* This is a simple class that will connect to a PH server and
* return the results of a query. 
*
* Example:
* $ph = new PH ("Eric Carter");
*    all the matched would be stored in "$ph->matches" and
*    the responses in "$ph->responses";
* -or-
* $ph = new PH ("alias=ecarter");
* 
*/
class PH 
{
    //  
    public $host = 'qi.uci.edu';
    public $port = 105;
    public $timeout = 120;
    public $socket = 0;
    public $errors = array();
    public $responses = array();
    public $matches = array();
    public $socket_status = array();

    public function __construct($query_string = "") 
    {
        // Builds a new object
        /*
        if ($query_string) {
            $this->query($query_string);
        }*/
    }

    public function __destruct()
    {
        $this->closeSocket();
    }

    public function query($query_string) 
    {
        // Open a new socket
        if (!$this->socket) 
        {
            $this->openSocket();
        }
        // Invoke query
        fputs($this->socket, "query $query_string\n\r");

        // Receive response from the PH server
        $response = trim(fgets($this->socket, 128));
        if (preg_match("/^(\d+):(.*)/", $response, $matches)) 
        {
            $this->responses[$matches[1]] = $matches[2];
        }
        $this->matches = array();
        // Get current socket status
        $this->socket_status = socket_get_status($this->socket);

        // Make sure to break the loop if there are no more bytes
        while ($this->socket_status[unread_bytes] > 0) 
        {
            $stream = trim(fgets($this->socket, 2048));
            if (preg_match("/^-(\d+):(\d+):(?:\s*([^:]+):)?\s*(.*)/",$stream, $matches)) 
            {

                $match_no = $matches[2];

                if ($prev_match_no < $match_no) 
                { 
                	$match = array(); 
                }

                $match_field = $matches[3];
                $match_value = $matches[4];

                $match[$match_field] = $match_value;
                $this->matches[$match_no] = $match;

                $prev_match_no = $match_no;
            }
            elseif (preg_match("/^(\d+):(.*)/", $stream, $matches)) 
            {
                $this->responses[$matches[1]] = $matches[2];
            }
            $this->socket_status = socket_get_status($this->socket);
        }

        return $this->matches;
        // Close the socket
        //$this->closeSocket();
    }

    public function openSocket() 
    {
        // Opens a socket to the PH server
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        // Error checking, determine if we have a valid socket
        if (!$this->socket) 
        {
            $this->errors[] = "$errstr ($errno)";
            return false;
        }
        else 
        {
            socket_set_timeout($this->socket, $this->timeout);
            socket_set_blocking($this->socket, true);
            return true;
        }
    }
    public function closeSocket() 
    {
        // Closes an open PH server socket
        if ($this->socket) 
        {
            fclose($this->socket);
        }
    }
}
?>