<?php
namespace Cache;

class Memcache implements CacheInterface{
	
	var $memcache;
	
	public function __construct(){
		if(extension_loaded('memcache')){
			$this->memcache = new \Memcache;
			$this->memcache->connect('localhost', 11211);
		}else{
			return false;
		}
	}
	
	public function set($key, $data, $expire = 0)
	{
		return $this->memcache->set(sha1($key), $data, false, $expire);
	}
	
	public function get($key)
	{
		return $this->memcache->get(sha1($key));
	}
	
	public function delete($key)
	{
		return $this->memcache->delete(sha1($key));
	}
	
	public function purge(){
		return $this->memcache->flush();
	}
	
}