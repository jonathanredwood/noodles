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
		return $view->generate('/UIElements/Menu.php', array('menu'=>$this->getSiteStructure()));
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
											AND url LIKE :parent");
			$this->core->db->bind_value(':depth', $depth, 'int');
			$this->core->db->bind_value(':parent', $parent.'%', 'string');
				
		}else{
			$this->core->db->prepare("SELECT url, menuTitle FROM pages WHERE menuShow = 1 AND LENGTH(url) - LENGTH(REPLACE(url, '/', '')) = 0");
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

		
	public function draw(){
		return $this->recursiveDraw($this->array);
	}
	
	
	public function recursiveDraw($array, $depth = 0){
		$depth++;
		$output = '<ul>';
		foreach($array as $item){
				
			if(isset($item['menuTitle'])){
				$output .= '<li><a href="'.$item['url'].'">'.$item['menuTitle'].'</a></li>';
			}
			
			if(isset($item['children'])){
				$output .= recursiveDraw($item['children'],$depth);
			}
		}
		$output .= '</ul>';
		return $output;
	}
}