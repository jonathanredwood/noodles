<?php
namespace Database;

interface DatabaseInterface{
	
	/**
	 * Creates a connection to the MySQL database
	 * @param $ip string
	 * @param $user string
	 * @param $pass string
	 * @param $db string
	 */
	public function connect($ip, $user, $pass, $db);
	
	/**
	 * Runs Query, returns result and logs queries and performance
	 * @param $query string
	 * @return boolean
	 */
	public function query($query);
		
	/**
	 * Fetch an assosiative array of all the data returned by the query
	 * @param $query string
	 * @return array
	 */
	public function queryAll($query);
	
	/**
	 * Fetch an assosiative array of the first row returned by the query
	 * @param $query string
	 * @return array
	 */
	public function queryFirst($query);

	/**
	 * Prevents some injection techniques
	 * @param $query string
	 * @return string
	 */
	public function escape($query);

	/**
	 * Returns the number of rows for a given query
	 * @param $query string
	 * @return integer
	 */
	public function num_rows($query);

	/**
	 * Closes the connection to the database
	 * @return boolean
	 */
	public function close();

	/**
	 * Builds an array containing logged data for use in the developer console
	 * @return array
	 */
	public function consoleData();
	
	//public function prepare();
	
	//public function execute();
	
	//public function bind_value();
}
?>