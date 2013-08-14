<?php
namespace Cache;

class Disk implements CacheInterface{
	
	
	public function set($key, $data, $expire = 0)
	{
		if($expire != 0){
			$expiry = time() + $expire;
		}else{
			$expiry = '0';
		}
		$serialized = serialize(array('expires'=>$expiry, 'data'=>$data));
		return file_put_contents('./cache/'.sha1($key).'.cache', $serialized);
	}
	
	public function get($key)
	{
		$filename = sha1($key);
		if(file_exists('./cache/'.$filename.'.cache')){
			$data = unserialize(file_get_contents('./cache/'.$filename.'.cache'));
			
			if($data['expires'] == 0 || $data['expires'] > time()){
				return $data['data'];				
			}else{
				unlink('./cache/'.$filename.'.cache');
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function delete($key){
		$filename = sha1($key);
		if(file_exists('./cache/'.$filename.'.cache')){
			return unlink('./cache/'.$filename.'.cache');
		}
	}
	
	public function purge(){
		$directory = './cache/';
		if(!$dh=opendir($directory)){
			return false;
		}
		while($file=readdir($dh)){
			if($file == "." || $file == ".."){
				continue;
			}
			if(is_file($directory."/".$file)){
				unlink($directory."/".$file);
			}
		}
		closedir($dh);
	}
	
	public function cleanup(){
		
	}
}