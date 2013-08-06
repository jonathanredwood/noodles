<?php
namespace Database;
/**
 * @author Jonathan
 */
class MySQLi implements DatabaseInterface{
	
	/**
	 * Database link
	 * @var mysqli
	 */
	var $link;
	
	/**
	 * Log of queries and performance metrics
	 * @var array
	 */
	var $queries = array();
	
	/**
	 * the current result from a prepared statement
	 * @var mysqli_result
	 */
	var $result;
	
	/**
	 * current prepared statement
	 * @var mysqli_stmt
	 */
	var $statement;
	
	var $prepQuery;
	
	/**
	 * parameters to be bound to the current prepared statement
	 * @var array
	 */
	var $parameters = array();
		
	/**
	 * Enables or disables the log to console feature
	 * @var bool
	 */
	var $logging = true;

	/**
	 * Creates a connection to the MySQL database
	 * @param $ip string
	 * @param $user string
	 * @param $pass string
	 * @param $db string
	 */
	public function connect($ip, $user, $pass, $db)
	{
		$this->link = new \mysqli($ip, $user, $pass, $db) or die("Cannot connect to DB!");
	}
	
	/**
	 * Runs MySQL query, returns result and logs queries and performance
	 * @param $query string 
	 * @return mysqli_result
	 */
	public function query($query)
	{
		$start = microtime(true); // Get the time before
		$result = $this->link->query($query); // run the query		
		$end = microtime(true); // Get the time after
		$time = $end - $start;

		if($this->logging){
			$this->queries[] = array('query' => $query, 'time' => $time, 'source' => self::get_backtrace(), 'error'=>$this->link->error);
		}
		return $result;
	}
	
	/**
	 * Fetch an assosiative array of all the data returned by the query
	 */
	public function queryAll($query)
	{
		$result = self::query($query);
		if($result){
			return $result->fetch_all(MYSQLI_ASSOC);
		}
	}
	
	/**
	 * Fetch an assosiative array of the first row returned by the query
	 */
	public function queryFirst($query)
	{
		$result = self::query($query);
		return $result->fetch_assoc();
	}
	
	/**
	 * Prevents some SQL injection techniques
	 */
	public function escape($query)
	{
		return $this->link->real_escape_string($query);
	}
	
	/**
	 * Returns the number of rows for a given query
	 */
	public function num_rows($query)
	{
		$result = self::query($query);
		return $result->num_rows;
	}
	
	public function affected_rows(){
		return $this->link->affected_rows;
	}
	
	/**
	 * Set the query with question marks indicating variables and create the statement object
	 * @param string $query
	 */
	public function prepare($query)
	{		
		$this->prepQuery = $query;
		
		$start = microtime(true); // Get the time before
		$this->statement = $this->link->prepare($query);
		$end = microtime(true); // Get the time after
		$time = $end - $start;
		
		if($this->logging){	
			$this->queries[] = array('query' => 'Preparing: '.$query, 'time' => $time, 'source' => self::get_backtrace(), 'error'=>$this->link->error);
		}
	}
	
	/**
	 * Adds a parameter to the array
	 */
	public function bind_value($parameter = null, $value, $type = 'string')
	{
		switch(strtolower($type)){
			case 'string':
				$type = 's';
				break;
			case 'int':
				$type = 'i';
				break;
			case 'bool':
				$type = 'b';
				break;
			case 'double':
				$type = 'd';
				break;
			case 'blob':
				$type = 'b';
				break;
		}
		$this->parameters[] = array('type'=>$type, 'value'=>$value);
	}
	
	/**
	 * Bind parameters, execute the query and set the current result
	 */
	public function execute()
	{
		if($this->statement){
			$types = '';
			$params = array();
			foreach($this->parameters as $parameter){
				$types .= $parameter['type'];
				$params[] = $parameter['value'];
			}
		
			$this->parameters = array(); // clean up
		
			array_unshift($params, $types);
		
			$refs = array();
			foreach($params as $key => $value){
				$refs[$key] = &$params[$key];
			}
		
			call_user_func_array(array($this->statement, 'bind_param'), $refs);
		
			// Work out what the resulting query string would be
			$currentQuery = $this->prepQuery;
			$count = substr_count($currentQuery, '?');
			for($i = 1; $i <= $count; $i++){
				$value = $params[$i];
				if(is_string($value)){
					$value = "'".$value."'";
				}
				$value = '<span style="color:#aaa">'.$value.'</span>';
				$currentQuery = preg_replace('/\?/', $value, $currentQuery, 1);
			}
		
			$start = microtime(true); // Get the time before
	
			$this->statement->execute(); // Runs the query with the provided parameters
	
			$end = microtime(true); // Get the time after
			$time = $end - $start;
			
			if($this->logging){
				$this->queries[] = array('query' => 'Executing: '.$currentQuery, 'time' => $time, 'source' => self::get_backtrace(), 'error'=>$this->statement->error);
			}
			
			$this->result = $this->statement->get_result();
			
			return true;
		}else{
			return false;
		}
	}


	
	/**
	 * Fetch an assosiative array of all the data returned by the prepared query
	 */
	public function prepQueryAll()
	{
		return $this->result->fetch_all(MYSQLI_ASSOC);
	}

	/**
	 * Fetch an assosiative array of the first row returned by the prepared query
	 */
	public function prepQueryFirst()
	{
		return $this->result->fetch_row();
	}
	
	/**
	 * Returns the number of rows from the last prepared query
	 */
	public function prepNumRows()
	{
		return $this->result->num_rows;
	}
	
	/**
	 * Returns the ID of the last inserted row
	 */
	public function lastInsertId()
	{
		return $this->link->insert_id();
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
	
	/**
	 * Closes the connection to the database
	 */
	public function close()
	{
		$this->link->kill($this->link->thread_id);
		return $this->link->close();
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