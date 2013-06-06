<?php
/**
 * Performance logging
 * @author Jonathan
 */
class Performance{
	
	public $points;

	/**
	 * Creates initial point 
	 */
	public function __construct()
	{
		$this->points = array();
		$this->points[] = array('name'=>'Start', 'time'=>microtime(true), 'total' => 0, 'current'=>0 );
	}
	
	/**
	 * Adds a new point
	 * @param string $name
	 */
	public function addPoint($name)
	{
		$current = round(microtime(true) - $this->points[count($this->points)-1]['time'], 3);
		$total = $this->points[count($this->points)-1]['total']+$current;
		$this->points[] = array('name'=>$name, 'time'=>microtime(true), 'total'=>$total, 'current'=>$current );
	}
	
	/**
	 * Outputs an array for the developer panel
	 * @return array
	 */
	public function consoleData()
	{
		$output = array('title'=>'Performance', 'id'=>'', 'data'=>array());
		
		foreach($this->points as $point){
			$output['data'][] = array(
				'Name'			=> array('show' => true, 'text' =>  $point['name']), 
				'Current (s)'	=> array('show' => true, 'text' => number_format($point['current'],3)), 
				'Total (s)'		=> array('show' => true, 'text' => number_format($point['total'],3)),
			);
		}
		return $output;
	}

	/**
	 * Gets the time in seconds since the framework started running
	 */
	public function getRuntime(){
		return $current = round(microtime(true) - $this->points[0]['time'], 5);
	}
}