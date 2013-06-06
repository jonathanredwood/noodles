<?php 

class PageApplication extends Application{
	
	var $showSkin = false;
	
	public function __construct(){
		parent::__construct();
	}
		
	public function run(){		
		
		// Are we using the tides?
		if($this->core->db->num_rows("SELECT * FROM levels WHERE tides = 1")){

			// Get the high tide time and cache it
			$cache = new Cache('disk');

			if(!$hightime = $cache->get("hightime")){
				// 
				$xml = simplexml_load_file("http://www.tidetimes.org.uk/plymouth-devonport-tide-times.rss");
				$lines = explode('<br/>', $xml->channel->item->description);
				$hightime = substr($lines[3],0,2) + (substr($lines[3],3,2)/60);
				$cache->set("hightime", $hightime, 600);
			}
			
			$now = gmdate('G') + 1 + gmdate('i')/60;
			
			$minusone = $this->getLevel($hightime, $now - 1);
			$current = $this->getLevel($hightime, $now);
			$plusone = $this->getLevel($hightime, $now + 1);

			$this->core->db->query("UPDATE levels SET A = ".$minusone.", B = ".$current.", C = ".$plusone." WHERE id = 1");
		}
		
		// Get the levels from the database and output them
		$results = $this->core->db->queryFirst("SELECT A, B, C FROM levels WHERE id = 1");
		foreach($results as $result){
			$output .= $result . ',';
		}
		$this->content['data'] = rtrim($output,',');
	}
	
	public function getLevel($high, $time){
		return cos(pi()/6.21 * ($time - $high)) * 50 + 50;
	}
}

?>