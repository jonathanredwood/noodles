<h1>Page management</h1>

<?php //if(isset($_GET['pageid']) && $_GET['pageid'] != 0): ?>
<a href="?pageid=<?php echo $parentid ?>">Back</a>
<?php //endif; ?>

<?php if(isset($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'add')): ?>

	<form id="page" method="post" action="?action=<?php if(isset($_GET['action'])) echo $_GET['action'] ?>&pageid=<?php if(isset($_GET['pageid'])) echo $_GET['pageid'] ?>">
		
		<div class="section left">
		<label for="menuTitle">Menu Title</label>
		<input name="menuTitle" type="text" value="<?php if(isset($pagedata['menuTitle'])) echo $pagedata['menuTitle'] ?>">
		
		<label for="title">Browser Title</label>
		<input name="title" type="text" value="<?php if(isset($pagedata['title'])) echo $pagedata['title'] ?>">
		
		<label for="url">URL</label>
		<input name="url" type="text" value="<?php if(isset($pagedata['url'])) echo $pagedata['url'] ?>">
		
		<label for="application">Application</label>
		<select name="application">
		<?php foreach($applications as $application): ?>
			<?php if($_GET['action'] == 'edit'): ?>
			<option <?php if(isset($pagedata['application']) && $application['id'] == $pagedata['application']) echo 'selected' ?> value="<?php echo $application['id'] ?>">
			<?php elseif($_GET['action'] == 'add'): ?>
			<option <?php if($application['displayName'] == 'Basic') echo 'selected' ?> value="<?php echo $application['id'] ?>">
			<?php endif; ?>
				<?php echo $application['displayName'] ?>
			</option>
		<?php endforeach; ?>
		</select>
		
		<label for="menuShow">Show in menu:</label>
		<input type="checkbox" name="menuShow" <?php if(isset($pagedata['menuShow']) && $pagedata['menuShow']) echo 'checked="checked"' ?>" >
		</div>
		
		<div class="section right">
		<label for="groups[]">Groups</label>
		<select name="groups[]" multiple size="<?php echo count($groups) ?>">
		<?php foreach($groups as $group): ?>
			 <option <?php if($_GET['action'] == 'add' || $group['allow']) echo 'selected' ?> value="<?php echo $group['teamID'] ?>"><?php echo $group['displayName'] ?></option>
		<?php endforeach; ?>
		</select>
		
		</div>
		
		<div class="section full">
		<label for="copy">Copy</label>
		<textarea name="copy"><?php if(isset($pagedata['copy'])) echo $pagedata['copy'] ?></textarea>
		
		<input type="submit" name="submit" value="submit"/>
		</div>
	</form>

<?php else: ?>
	<a href="?action=add">Add Page</a>

<?php endif; ?>


<?php echo $output; ?>