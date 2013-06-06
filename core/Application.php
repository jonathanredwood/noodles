<?php
/**
 * Core application class
 * @author Jonathan
 */
abstract class Application{
	
	var $request;
	var $content = array();
	var $showSkin = true;
	var $theme = 'default';
	
	protected $core;
		
	public function __construct()
	{
		$this->request = $this->getRequest();
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
		
	public function getRequest()
	{
		//get current request
		$request = explode('?',$_SERVER['REQUEST_URI']);
		$request = trim($request[0], '/');
		if(empty($request)) $request = 'index';
				
		// 404 handling
		if(!file_exists( 'applications/'. $request . '_app.php' ) && !file_exists( 'templates/'. $request . '_tpl.php' )) $request = 'pagenotfound';
		
		return $request;
	}
}

?>