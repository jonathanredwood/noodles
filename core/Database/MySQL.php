<?php
namespace Database;

/**
 * Support for legacy MySQL
 * @deprecated
 * @author Jonathan
 */
class MySQL implements DatabaseInterface{
	
	var $link;
	var $queries = array();
	
	var $parameters;
	var $prepQuery;
	var $result;
	
	/**
	 * Enables or disables the log to console feature
	 * @var bool
	 */
	var $logging = true;
	
	public function connect($ip, $user, $pass, $db)
	{		
		$this->link = mysql_connect($ip, $user, $pass) or die("Cannot connect to DB!");
		mysql_select_db($db, $this->link);
	}
	
		
	public function query($query)
	{		
		$start = microtime(true);
		$result = mysql_query($query, $this->link); // run the query
		$end = microtime(true);
		$time = $end - $start;
		
		if($this->logging){
			$this->queries[] = array('query' => $query, 'time' => $time, 'source'=>self::get_backtrace());
		}

		return $result;
	}
	
	
	public function queryAll($query)
	{
		$result = self::query($query);
		if($result){
			$resultArray = array();
			while(($resultArray[] = mysql_fetch_assoc($result)) || array_pop($resultArray));
			return $resultArray;
		}
	}
	
	
	public function queryFirst($query)
	{
		$result = self::query($query);
		return mysql_fetch_assoc($result);
	}
	
	
	public function escape($query)
	{
		return mysql_real_escape_string($query, $this->link);
	}
	
	
	public function num_rows($query)
	{
		$result = self::query($query);
		return mysql_num_rows($result);
	}
	
	
	/**
	 * The old MySQL driver does not support prepared statements yet we use them throughout the code
	 * 
	 * To get around this we can emulate prepared statements to work like in MySQLi
	 */
	
	public function prepare($query)
	{
		$this->prepQuery = $query;
	}
	
	public function bind_value($parameter = null, $value, $type = 'string')
	{
		$this->parameters[] = array('type'=>$type, 'value'=>$value);
	}
	
	public function prepQueryFirst()
	{
		return mysql_fetch_assoc($this->result);
	}
	
	public function prepQueryAll()
	{
		if($this->result){
			$resultArray = array();
			while(($resultArray[] = mysql_fetch_assoc($this->result)) || array_pop($resultArray));
			return $resultArray;
		}
	}
	
	public function prepNumRows()
	{
		return mysql_num_rows($this->result);
	}
	
	/**
	 * Returns the ID of the last inserted row
	 */
	public function lastInsertId()
	{
		return mysql_insert_id($this->link);
	}
	
	public function execute()
	{
		$currentQuery = $this->prepQuery;
		
		$params = array();
		foreach($this->parameters as $parameter){
			$types .= $parameter['type'];
			$params[] = $parameter['value'];
		}
				
		$count = substr_count($currentQuery, '?');
		for($i = 0; $i <= $count; $i++){
			$value = $params[$i];
			if(is_string($value)){
				$value = self::escape($value);
				$value = "'".$value."'";
			}
			
			$currentQuery = preg_replace('/\?/', $value, $currentQuery, 1);
		}

		return $this->result = self::query($currentQuery);
	}
	
	/**
	 * Allows suppression of logging for queries that need to be hidden
	 */
	public function disableLogging()
	{
		$this->logging = false;
	}
	
	/**
	 * Turns logging of queries on again
	 */
	public function enableLogging()
	{
		$this->logging = true;
	}
	
	public function close()
	{
		return mysql_close($this->link);
	}
	
	
	/**
	 * Returns the file and line which the query was made
	 * @return string
	 */
	private function get_backtrace()
	{
		$debug_backtrace = debug_backtrace();
		foreach($debug_backtrace as $key => $backtrace){
			if($backtrace['class'] != get_class($this)){
				return $source =  str_ireplace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/',$debug_backtrace[$key]['file'])) . ':'.$debug_backtrace[$key]['line'];
			}
		}
	}
	
	/**
	 * Builds an array containing logged data for use in the developer console 
	 */
	public function consoleData()
	{
		$output = array('title'=>'Database', 'id' => '', 'data'=>array());
	
		foreach($this->queries as $indiv){
		
			$query = $indiv['query'];
			$time = number_format(round($indiv['time'], 4),4);
	
			$query = nl2br($query);
				
			if($indiv['error']){
				$query .= '<p style="color:red;">Error: '. $indiv['error'] .'</p>';
			}	
				
			$output['data'][] = array(
				'Query' => array('show' => true, 'text' => $query), 
				'Location' => array('show' => true, 'text' => $indiv['source']),
				'Time (s)' => array('show' => true, 'text' => $time),
			);
		}
		return $output;
	}
	
}

?>