<?php
function recursiveDraw($array, $depth = 0){
	$depth++;
	$output = '<ul>';
	foreach($array as $item){
			
		if(isset($item['menuTitle'])){
			$output .= '<li><a href="'.$item['url'].'">'.$item['menuTitle'].'</a></li>';
		}
		
		if(isset($item['children'])){
			$output .= recursiveDraw($item['children'],$depth);
		}
	}
	$output .= '</ul>';
	return $output;
}

echo recursiveDraw($menu);
?>