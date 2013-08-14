<?php

/**
 * Collection of utility functions
 * @author Jonathan
 */

class Util{


	public function translateRange($a1, $a2, $b1, $b2, $num){
		if(($a2 - $a1) != 0){
			$c = (($num - $a1) * ($b2 - $b1)/($a2 - $a1)) + $b1;
		}else{
			$c = 1;
		}
		return $c;
	}
	
	/**
	 * Find all URLs in a string and add anchor tags
	 */
	public function auto_link_text($text){
		return preg_replace('@(http)?(s)?(://)?(([-\w]+\.)+([^\s]+)+[^,.\s])@', '<a target="_blank" rel="nofollow" href="http$2://$4">$1$2$3$4</a>', $text);
	}
	
	
	/**
	 * Generates a random string of specified length
	 */
	public function createRandomKey($amount){
		$keyset  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$randkey = "";
		for ($i=0; $i<$amount; $i++){
			$randkey .= substr($keyset, rand(0, strlen($keyset)-1), 1);
		}
		return $randkey;	
	}
	
	
	/**
	 * Returns the IP address of the visitor
	 */
	public function getIP(){
	    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }else{
			$ip = $_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}

	
	public function sortArrayByArray(array $toSort, array $sortByValuesAsKeys){
		$commonKeysInOrder = array_intersect_key(array_flip($sortByValuesAsKeys), $toSort);
		$commonKeysWithValue = array_intersect_key($toSort, $commonKeysInOrder);
		$sorted = array_merge($commonKeysInOrder, $commonKeysWithValue);
		return $sorted;
	}
		
	public function fetch_contents($url){
		if(function_exists('curl_init')){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$content = curl_exec($ch);
			if ($content === false) {
				return false;
				//throw new Exception('Can not download URL');
			}
			curl_close($ch);
		}else{
			$content = file_get_contents($url);
		}
		return $content;
	}
}

?>