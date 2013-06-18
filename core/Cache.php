<?php

require_once 'Cache/CacheInterface.php';
require_once 'Cache/Disk.php';
require_once 'Cache/Memcache.php';

/**
 * Allows the storage of data between requests and users
 * @author Jonathan
 */

class Cache{
	
	var $handler;
	
	public function __construct($type = 'disk')
	{
		switch(strtolower($type)){
			case 'disk':
				$this->handler = new Cache\Disk;
				break;
			case 'memcache':
				$this->handler = new Cache\Memcache;
				break;
		}
	}
	
	public function set($key, $data, $expire = 0)
	{
		return $this->handler->set($key, $data, $expire);
	}
	
	public function get($key)
	{
		return $this->handler->get($key);
	}
	
	public function delete($key)
	{
		return $this->handler->delete($key);
	}
	
	public function purge()
	{
		return $this->handler->purge();
	}
}


?>