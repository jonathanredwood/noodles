<?php

namespace Cache;

interface CacheInterface{
	

	/**
	 *	Store a value with the provided key 
	 *	@param String $key, 
	 *	@param Any $data
	 *	@param Int $expire
	 */
	public function set($key, $data, $expire = 0);
	
	/**
	 * Fetches value stored with key
	 * @param String $key
	 */
	public function get($key);
	
	/**
	 * Deletes the value stored with key
	 * @param String $key
	 */
	public function delete($key);
	
	/**
	* Removes all values stored
	*/
	public function purge();
}

?>