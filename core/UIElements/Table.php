<?php 
/**
 * Table View
 * 
 * @param $class
 * @param $id
 * @param $data
 * 
 * @author Jonathan
 */
?>
<?php if(!empty($data)): ?>
	<table id='<?php if(isset($id)) echo $id ?>' class='<?php if(isset($class)) echo $class ?>'>
	<?php $ordering = array(); ?>
		<tr>
	<?php 
	$combinedheadings = '';
	foreach($data[0] as $heading => $heading_data){
		$combinedheadings .= $heading;
	}	
	?>	
	<?php if($data && !empty($combinedheadings)): ?>
		<?php foreach($data[0] as $heading => $heading_data): ?>
			<?php if($heading_data['show']): ?>
				<th>
					<?php if(isset($heading_data['sortable'])): ?>
						<?php 						
							$orderlink = 'desc';
							if($pagination['sort'] == $heading_data['sortname']){
								if($pagination['order'] == 'asc') $orderlink = 'desc';
								if($pagination['order'] == 'desc') $orderlink = 'asc';
							}
						?>
						<a href="<?php echo '/'.$request.'/?sort='.urlencode($heading_data['sortname']).'&amp;order='.$orderlink ?>">
					<?php endif; ?>		
						
						<?php echo $heading ?>
					
					<?php if(isset($heading_data['sortable'])): ?>
						</a>
					<?php endif; ?>		
				</th>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
		</tr>
	
	<?php if ($data): ?>
		<?php foreach ($data as $player): ?>
			<tr>
			<?php foreach ($player as $item): ?>
				<?php if($item['show']): ?>
				<td<?php if(isset($item['class'])) echo ' class="'.$item['class'].'"' ?> style="<?php if(isset($item['colour'])) echo 'color:'.$item['colour'].'; ' ?><?php if(isset($item['center'])) echo 'text-align: center; ' ?>">
				
					<?php if(isset($item['href'])): ?>
					<a href="<?php echo $item['href'] ?>">
					<?php endif; ?>
					
						<?php if(isset($item['image'])): ?>
						<img <?php if(isset($item['image']['title'])) echo 'title="'.$item['image']['title'].'" ' ?> src="<?php echo $item['image']['src'] ?>"<?php if(isset($item['image']['width'])) echo ' width="'.$item['image']['width'].'"' ?><?php if(isset($item['image']['height'])) echo ' height="'.$item['image']['height'].'"' ?>/>
						<?php endif; ?>
						
						<?php if(isset($item['text'])) echo $item['text'] ?>
							
					<?php if(isset($item['href'])): ?>
					</a>
					<?php endif; ?>		
					
				</td>
				<?php endif; ?>
			<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	</table>
<?php endif; ?>