<?php

/**
 * Database Class
 * @author Jonathan
 */
require_once 'Database/DatabaseInterface.php';

class Database{
	
	var $handler;
	
	public function __construct($type = 'mysqli')
	{
		switch(strtolower($type)){
			case 'mysql':
				$this->handler = new Database\MySQL;
				break;
			case 'mysqli':
				$this->handler = new Database\MySQLi;
				break;
			case 'pdo':
				$this->handler = new Database\PDO;
				break;
		}
	}
	
	public function connect($ip, $user, $pass, $db)
	{		
		return $this->handler->connect($ip, $user, $pass, $db);
	}
	
		
	public function query($query)
	{		
		return $this->handler->query($query);
	}
	
	
	public function queryAll($query)
	{
		return $this->handler->queryAll($query);
	}
	
	
	public function queryFirst($query)
	{
		return $this->handler->queryFirst($query);
	}
	
	
	public function escape($query)
	{
		return $this->handler->escape($query);
	}
	
	
	public function num_rows($query)
	{
		return $this->handler->num_rows($query);
	}
	
	public function affected_rows()
	{
		return $this->handler->affected_rows();
	}
	
	
	/* Prepared statements */
	public function prepare($query)
	{
		return $this->handler->prepare($query);
	}
		
	public function bind_value($parameter, $value, $type){
		return $this->handler->bind_value($parameter, $value, $type);
	}
	
	public function prepQueryFirst()
	{
		return $this->handler->prepQueryFirst();
	}
		
	public function prepQueryAll()
	{
		return $this->handler->prepQueryAll();
	}
	
	public function prepNumRows()
	{
		return $this->handler->prepNumRows();
	}
	
	public function lastInsertId()
	{
		return $this->handler->lastInsertId();
	}
	
	public function execute()
	{
		return $this->handler->execute();
	}
	
	public function disableLogging()
	{
		return $this->handler->disableLogging();
	}
	
	public function enableLogging()
	{
		return $this->handler->enableLogging();
	}
	
	public function close()
	{
		return $this->handler->close();
	}
	
	
	public function consoleData()
	{
		return $this->handler->consoleData();
	}
	
}

?>