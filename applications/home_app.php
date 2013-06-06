<?php 

class PageApplication extends Application{
	
	public function __construct(){
		parent::__construct();
	}
		
	public function run(){
		if(isset($_GET['tides']) && $_GET['tides'] == 1){
			$this->core->db->query("UPDATE levels SET tides = 1 WHERE id = 1");
		}
		if(isset($_GET['A']) && is_numeric($_GET['A'])){
			$this->core->db->query("UPDATE levels SET A = ".$_GET['A'].", tides = 0 WHERE id = 1");
		}
		if(isset($_GET['B']) && is_numeric($_GET['B'])){
			$this->core->db->query("UPDATE levels SET B = ".$_GET['B'].", tides = 0 WHERE id = 1");
		}
		if(isset($_GET['C']) && is_numeric($_GET['C'])){
			$this->core->db->query("UPDATE levels SET C = ".$_GET['C'].", tides = 0 WHERE id = 1");
		}
	}
}

?>