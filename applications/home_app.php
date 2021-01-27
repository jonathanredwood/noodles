<?php 

class PageApplication extends Application{
	
	public function __construct(){
		parent::__construct();
	}
		
	public function run(){
		$this->content['value'] = 'bananas';
	}
}

?>