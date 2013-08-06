<?php 

class PageApplication extends Application{
		
	public function __construct(){
		parent::__construct();
	}
		
	public function run(){
		
		$this->core->db->prepare("SELECT url, menuTitle FROM pages 
									WHERE menuShow = 1 
										AND url LIKE CONCAT(:parent,'%')
										AND url != :parent
										ORDER BY ordering");
	
		$this->core->db->bind_value(':parent', $this->request, 'string');

		$this->core->db->execute();
		
		if($child = $this->core->db->prepQueryFirst()){
			header('Location: /'.$child['url']);
			exit;
		}
	}
}

?>