<?php
/**
 * Generates Developer Panel
 * @author Jonathan
 */
class DeveloperPanel{
	
	protected $tables = array();
	protected $panels = array();
	
	/**
	 * Adds table data to be displayed
	 * @param array $table
	 */
	public function addTable($table)
	{
		$this->tables[] = $table;
	}
	
	public function addPanel($panel)
	{
		$this->panels[] = $panel;
	}

	/**
	 * Draws the Developer Panel
	 * @return string
	 */
	public function draw($theme = 'default')
	{
		$output = '<div id="devpanel">
					<link rel="stylesheet" type="text/css" href="/themes/'.$theme.'/css/debug.css"/>
					<script src="/themes/'.$theme.'/javascript/debug.js"></script>
					<span class="devpanel-title">Developer Panel</span>
					<span class="devpanel-copyright">&copy; '.date('Y').' - <a style="color:#fff;" href="http://jred.co.uk">jred.co.uk</a></span>
					<br><span class="devpanel-copyright"><a style="color:#fff;" href="?dev=0">Exit Developer Mode</a></span>';
		
		foreach($this->panels as $panel){
			$output .= self::drawPanel($panel);
		}
		foreach($this->tables as $table){
			$output .= self::drawTable($table);
		}	
		$output .= '<span class="debug-toggle" /></span></div>';
		
		return $output;
	}
	
	/**
	 * Draws HTML for individual tables
	 * @param array $table
	 */
	private function drawTable($table)
	{	
		$output = '<span class="devpanel-table-title">'.$table['title'].'</span>';

		if(isset($table['data'])){
			$view = new View();
			$output .= $view->generate('core/UIElements/Table.php', array('data' => $table['data']));
		}

		return $output;
	}
	
	private function drawPanel($panel)
	{
		if(!empty($panel)){
			$output = '<table><tr><td>'.$panel.'</td></tr></table>';
			return $output;
		}
	}
}

?>