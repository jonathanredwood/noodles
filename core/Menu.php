<?php
class Menu{
	
	protected $core;
	
	var $array;
	
	public function injectCore($core)
	{
		$this->core = $core;
	}
	
	public function generate()
	{
		$view = new View();
		$output = $view->generate('/UIElements/Menu.php', array('menu'=>$this->getSiteStructure()));
		$this->core->performance->addPoint('Menu Generated');
		return $output;
	}
	
	public function getSiteStructure()
	{
		$this->core->db->disableLogging(); // Turn off logging to avoid spamming console
		
		$output = $this->recursiveFetch();	
			
		$this->core->db->enableLogging(); // Re-enable logging
		
		$this->array = $output;
	
		return $output;
	}
		
	/**
	 * Creates an assosiative array of the site structure
	 * @param string $parent
	 * @return Ambigous <multitype:unknown , unknown>
	 */
	private function recursiveFetch($parent = false)
	{
		if($parent){
			$folder = trim($parent, '/');
			$depth = substr_count($folder, '/');
			$depth++; //First level items would be counted as root otherwise
		}else{
			$depth = 0; //Root
		}
		if($parent){
			$this->core->db->prepare("SELECT url, menuTitle FROM pages 
										WHERE menuShow = 1 
											AND LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = :depth
											AND url LIKE :parent
										ORDER BY ordering");
			$this->core->db->bind_value(':depth', $depth, 'int');
			$this->core->db->bind_value(':parent', $parent.'%', 'string');
				
		}else{
			// Get root pages
			$this->core->db->prepare("SELECT url, menuTitle FROM pages WHERE menuShow = 1 AND LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = 0 ORDER BY ordering");
		}
		$this->core->db->execute();
		$children = $this->core->db->prepQueryAll();
										
		$output = array();
		foreach($children as $key => $child){
			$output[$child['menuTitle']] = $child;
			//If there are children, add them
			if($newchildren = $this->recursiveFetch($child['url'])){
				$output[$child['menuTitle']]['children'] = $newchildren;
			}
		}
		return $output;
	}


}