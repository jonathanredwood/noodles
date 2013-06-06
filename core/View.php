<?php
/**
 * View Class
 * 
 * Populates a view template with data and uses ouput buffering to store it as a variable
 * 
 * @author Jonathan
 */
class View extends Core{

	public function generate($template, $data){		
		
		extract($data);

		ob_start();
		
		require $template;
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
}