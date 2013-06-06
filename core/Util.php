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
	 * Convert seconds to days, hours, minutes and seconds
	 * 
	 */
	public function secondsToTime($time){
		$seconds = $time%60;
		$mins = floor($time/60)%60;
		$hours = floor($time/60/60)%24;
		$days = floor($time/60/60/24);
		return $days .'d '. self::number_pad($hours) . 'h ' .self::number_pad($mins) . 'm ' ;//. self::number_pad($seconds) . 's';
	}
	
	
	/**
	 * Pad
	 */
	public function number_pad($number){
		return str_pad((int) $number,2,"0",STR_PAD_LEFT);
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
	
	/**
	* Geolocation API access
	*
	* @param    string  $ip         IP address to query
	* @param    string  $format     output format of response
	*
	* @return   string  XML, JSON or CSV string
	*/
	public function get_ip_location($ip, $format="xml") {
	
		$ip = explode(':',$ip);
		/* Set allowed output formats */
		$formats_allowed = array("json", "xml", "raw");
		
		require '/config.php';
	
		/* IP location query url */
		$query_url = "http://api.ipinfodb.com/v2/ip_query.php?key=".$CFG['geolocationKey']."&ip=";
	
		/* Male sure that the format is one of json, xml, raw.
		 Or else default to xml */
		if(!in_array($format, $formats_allowed)) {
			$format = "xml";
		}
	
		$query_url = $query_url . "{$ip[0]}&output={$format}";
	
		/* Init CURL and its options*/
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $query_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	
		/* Execute CURL and get the response */
		return curl_exec($ch);
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