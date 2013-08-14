<?php
/**
 * View Class
 * Populates a view template with data and uses ouput buffering to store it as a variable
 *
 * $view = new View();
 * $content = $view->generate('core/UIElements/Table.php', array('var1'=>'somedata', 'var2'=>'somedata'));
 *
 * @author Jonathan Redwood <jred.co.uk>
 */
class View extends Core{
 
        public function generate($template, $data){            
                extract($data); //Unpack the data array into variables named after the array keys
                ob_start(); // Start gathering output
               
                require $template; // Include the template file and generate the HTML
               
                $output = ob_get_contents(); // Get the content of the output buffer
                ob_end_clean();
                return $output;
        }
        
        public function __toString(){
        	
        }
}