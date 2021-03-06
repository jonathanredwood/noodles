<?php

class Template{
	
	var $application;
	var $showSkin;
	var $content;
	var $theme;

	protected $core;
	
	public function __construct($core, $application, $content, $theme = 'default', $showSkin = true){

		$this->core = $core;
		$this->application = $application;
		$this->showSkin = $showSkin;
		$this->content = $content;
		$this->theme = $theme;
	}
	
	public function getApplicationOutput(){

		//if there is a template output to it
		if(file_exists( 'templates/'. $this->application . '_tpl.php' )){
			$view = new View();
			return $view->generate('templates/'. $this->application . '_tpl.php', $this->content);
		}
	}
	
	public function buildOutput(){
	
		$this->content['application_output'] = $this->getApplicationOutput();
		$this->core->performance->addPoint('Application Template');
				
		if($this->showSkin){
			$view = new View();
			$output = $view->generate('themes/'.$this->theme.'/html/template.php', $this->content);
			$this->core->performance->addPoint('Skin Template');
		}else{
			$output = $this->content['application_output'];
		}
				

		$this->core->performance->addPoint('Output' .' ('.round(strlen($output)/1000,2).'KB)'  );
		return $output;
	}
}

?>