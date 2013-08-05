<?php
/**
 * Contains shared resources
 * @author Jonathan
 */
class Core{
	
	public $db;
	public $util;
	public $config;
	public $permissions;
	public $avatarcache;
	public $performance;
	
	public function __construct()
	{
		$this->performance =	new Performance();
		$this->performance->addPoint('Core Classes');
		
		require '/config.php';
		$this->config = $CFG;
				
		$this->db = new Database('PDO');
		$this->db->connect($CFG['mysqlIP'], $CFG['mysqlUser'], $CFG['mysqlPass'], $CFG['mysqlDB']);		
		$this->util =			new Util();
	}	
}
?>