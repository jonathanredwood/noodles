<div id="pagenav">

<?php

if(!isset($pagination['sort'])) $pagination['sort'] = '0';
if(!isset($pagination['order'])) $pagination['order'] = '0';

if($pagination['pages'] > 1){

	$x = 0;
	$pageNavNum = 5;
	if($pagination['pages'] > $pageNavNum){
		$pagesNext = 0;
		$pagesPrev = 0;
		$spareNext = false;
		$sparePrev = false;
		for($i = $pagination['currentPage']; $i < $pagination['pages'];$i++) $pagesNext++;
		if($pagesNext > $pageNavNum){
			$pagesNext = $pageNavNum;
			$spareNext = true;
		}

		for($i = $pagination['currentPage']; $i > 1;$i--) $pagesPrev++;
		if($pagesPrev > $pageNavNum){
			$pagesPrev = $pageNavNum;
			$sparePrev = true;
		}
		

				
		if($sparePrev){
			echo '<a href="?sort='.$pagination['sort'].'&amp;order='.$pagination['order'].'&amp;page=' . ($pagination['currentPage'] - 1) . '">' . PHP_EOL . '<img alt="arrow left" class="arrow" src="/themes/default/graphics/arrow-left.png"></a>' . PHP_EOL;
		}

		for($x = $pagination['currentPage']-$pagesPrev; $x <= $pagination['currentPage']+$pagesNext ; $x++){
			echo '<a href="?sort='.$pagination['sort'].'&amp;order='.$pagination['order'].'&amp;page=' .$x. '">'. $x . '</a>' . PHP_EOL;
		}
		if($spareNext){
			echo '<a href="?sort='.$pagination['sort'].'&amp;order='.$pagination['order'].'&amp;page=' . ($pagination['currentPage'] + 1) . '">' . PHP_EOL . '<img alt="arrow right" class="arrow" src="/themes/default/graphics/arrow-right.png"></a>' . PHP_EOL;
		}
	}else{
		for($x = 1;$x <= $pagination['pages']; $x++){
			echo '<a href="?sort='.$pagination['sort'].'&amp;order='.$pagination['order'].'&amp;page=' .$x. '">'. $x . '</a>' . PHP_EOL;
		}
	}
}
?>

</div>