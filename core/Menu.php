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
		$output = $view->generate('core/UIElements/Menu.php', array('menu'=>$this->getSiteStructure()));
		$this->core->performance->addPoint('Menu Generated');
		return $output;
	}
	
	public function getSiteStructure()
	{
		$this->core->db->disableLogging(); // Turn off logging to avoid spamming console
		
		$Pages = new Pages($this->core);
		$output = $Pages->recursive_site_structure();	
			
		$this->core->db->enableLogging(); // Re-enable logging
		
		$this->array = $output;
	
		return $output;
	}
		

}