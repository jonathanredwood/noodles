<?php
namespace Database;

class PDO implements DatabaseInterface{
	
	/**
	 * Database link
	 * @var PDO
	 */
	var $link;

	/**
	 * Log of queries and performance metrics
	 * @var array
	 */
	var $queries = array();
	
	/**
	 * the current result from a prepared statement
	 * @var result
	*/
	var $result;
	
	/**
	 * current prepared statement
	 * @var statement
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
		$dsn = 'mysql:dbname='.$db.';host='.$ip;
	
		try {
			$this->link = new \PDO($dsn, $user, $pass);			
		} catch (\PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
			return false;
		}
		return true;
	}
	
	/**
	 * Runs Query, returns result and logs queries and performance
	 * @param $query string
	 * @return boolean
	 */
	public function query($query)
	{
		$start = microtime(true); // Get the time before
		$result = $this->link->query($query); // run the query		
		$end = microtime(true); // Get the time after
		$time = $end - $start;

		if($this->logging){
			$this->queries[] = array('query' => $query, 'time' => $time, 'source' => self::get_backtrace(), 'error'=>@$this->link->errorInfo);
		}
		return $result;
	}
	
	/**
	 * Fetch an assosiative array of all the data returned by the query
	 * @param $query string
	 * @return array
	*/
	public function queryAll($query)
	{
		$result = self::query($query);
		if($result){
			return $result->fetchAll(\PDO::FETCH_ASSOC);
		}
	}
	
	/**
	 * Fetch an assosiative array of the first row returned by the query
	 * @param $query string
	 * @return array
	*/
	public function queryFirst($query)
	{
		$result = self::query($query);
		if($result){
			return $result->fetch(\PDO::FETCH_ASSOC);
		}
	}
	
	/**
	 * Prevents some injection techniques
	 * @param $query string
	 * @return string
	*/
	public function escape($string)
	{
		return mysql_real_escape_string($string);
	}
	
	/**
	 * Returns the number of rows for a given query
	 * @param $query string
	 * @return integer
	*/
	public function num_rows($query)
	{
		$result = self::query($query);
		if($result){
			return count($result->fetchAll(\PDO::FETCH_ASSOC));
		}else{
			return 0;
		}
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
			$this->queries[] = array('query' => 'Preparing: '.$query, 'time' => $time, 'source' => self::get_backtrace(), 'error'=>@$this->link->errorInfo);
		}
	}
	
	/**
	 * Adds a parameter to the array
	 */
	public function bind_value($parameter, $value, $type = 'string')
	{
		switch(strtolower($type)){
			case 'string':
				$type = \PDO::PARAM_STR;
				break;
			case 'int':
				$type = \PDO::PARAM_INT;
				break;
			case 'bool':
				$type = \PDO::PARAM_BOOL;
				break;
			case 'null':
				$type = \PDO::PARAM_NULL;
				break;
		}
		
		
		$this->parameters[] = array('parameter'=>$parameter, 'value'=>$value, 'type'=>$type);
	}
	
	/**
	 * Bind parameters, execute the query and set the current result
	 */
	public function execute()
	{
		if($this->statement){

			$currentQuery = $this->prepQuery;
			
			foreach($this->parameters as $parameter){
				
				$this->statement->bindValue($parameter['parameter'], $parameter['value'], $parameter['type']);
								
				if($parameter['type'] == \PDO::PARAM_STR){
					$currentQuery = str_replace($parameter['parameter'], "<span style='color:#aaa'>'".$parameter['value']."'</span>", $currentQuery);
				}else{
					$currentQuery = str_replace($parameter['parameter'], "<span style='color:#aaa'>".$parameter['value']."</span>", $currentQuery);
				}
			}
					
			$this->parameters = array(); // clean up
			
			$start = microtime(true); // Get the time before
	
			$this->statement->execute(); // Runs the query with the provided parameters
	
			$end = microtime(true); // Get the time after
			$time = $end - $start;
			
			if($this->logging){
				$this->queries[] = array('query' => 'Executing: '.$currentQuery, 'time' => $time, 'source' => self::get_backtrace(), 'error'=>@$this->statement->errorInfo);
			}	
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
		return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * Fetch an assosiative array of the first row returned by the prepared query
	 */
	public function prepQueryFirst()
	{
		return $this->statement->fetch(\PDO::FETCH_ASSOC);
	}
	
	/**
	 * Returns the number of rows from the last prepared query
	 */
	public function prepNumRows()
	{
		if($this->statement){
			return count($this->statement->fetchAll(\PDO::FETCH_ASSOC));
		}else{
			return 0;
		}
	}
	
	/**
	 * Returns the ID of the last inserted row
	 */
	public function lastInsertId()
	{
		return $this->link->lastInsertId();
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
	 * @return boolean
	*/
	public function close()
	{
		$this->link = null;
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
	 * @return array
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