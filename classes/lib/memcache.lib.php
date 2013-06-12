<?php

// this provides a connection interface to memcache
class CSDMemcache
{
	private $mem = null;
	private $prefix = "";
	
	public function __construct($prefix="")
	{
		//global $MEMC_SERVERS;
		$this->prefix = $prefix;
		$this->mem = new Memcache;
		
		//foreach($MEMC_SERVERS as $server)
		//{
		//	$part = explode(":",$server);
		//	$this->addServer($part[0],$part[1]);
		//}
		$this->addServer("127.0.0.1", "11211");
	}
	
	public function getStats()
	{
		return $this->mem->getExtendedStats();
	}
	
	public function __destruct()
	{
		$this->close();
	}
	
	public function addServer($host,$port="11211")
	{
		return $this->mem->addServer($host,$port,false);
	}
	
	public function set($k,$v,$exp=3600)
	{
		if(!$this->mem->replace($this->prefix.$k,$v,false,$exp))
		{
			return $this->mem->set($this->prefix.$k,$v,false,$exp);
		}
		else
		{
			return true;
		}
	}
	
	public function get($k)
	{
		return $this->mem->get($this->prefix.$k);
	}
	
	public function add($k,$v,$exp=3600)
	{
		return $this->mem->add($this->prefix.$k,$v,false,$exp);
	}
	
	public function replace($k,$v,$exp=3600)
	{
		return $this->mem->replace($this->prefix.$k,$v,false,$exp);
	}
	
	public function delete($k,$exp=1)
	{
		return $this->mem->delete($this->prefix.$k,$exp);
	}
	
	public function inc($k)
	{
		// NEED TO INCREMEMNT A VALUE
	}
	
	public function connect()
	{
		//return $this->mem->connect("127.0.0.1","11211");
	}
	
	public function flush()
	{
		$this->mem->flush();
	}
	
	public function close()
	{
		return $this->mem->close();
	}
}


?>
