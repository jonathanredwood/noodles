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
	
	public function getContent()
	{
		return $this->content;
	}
}

?>