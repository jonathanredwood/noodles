<?php
/**
 * Core application class
 * @author Jonathan
 */
abstract class Application{
	
	var $appname;
	var $request;
	var $content = array();
	var $showSkin = true;
	var $theme = 'default';
	
	protected $core;
		
	public function __construct()
	{
		
	}
	
	public function setAppName($appname)
	{
		$this->$appname = $appname;
	}
	
	public function setRequest($request)
	{
		$this->request = $request;
	}	
	
	public function injectCore($core)
	{
		$this->core = $core;
	}
	
	public function injectData($data)
	{
		$this->content = array_merge($this->content, $data);
	}
	
	public function generate_output(){
		$template = new Template($this->core, $this->appname, $this->content, $this->theme, $this->showSkin);
		return $template->buildOutput();
	}
	
	public function getContent()
	{
		return $this->content;
	}
}

?>