<h1>Page management</h1>

<?php //if(isset($_GET['pageid']) && $_GET['pageid'] != 0): ?>
<a href="?pageid=<?php echo $parentid ?>">Back</a>
<?php //endif; ?>

<?php if(isset($editor) && $editor): ?>

	<form method="post" action="?action=<?php if(isset($_GET['action'])) echo $_GET['action'] ?>&pageid=<?php if(isset($_GET['pageid'])) echo $_GET['pageid'] ?>">
		
		<label for="menuTitle">Menu Title</label><input name="menuTitle" type="text" value="<?php if(isset($pagedata['menuTitle'])) echo $pagedata['menuTitle'] ?>">
		<label for="title">Browser Title</label><input name="title" type="text" value="<?php if(isset($pagedata['title'])) echo $pagedata['title'] ?>">
		<label for="url">URL</label><input name="url" type="text" value="<?php if(isset($pagedata['url'])) echo $pagedata['url'] ?>">
		<label for="application">Application</label>
		<select name="application">
		<?php foreach($applications as $application): ?>
			 <option <?php if(isset($pagedata['application']) && $application['id'] == $pagedata['application']) echo 'selected' ?> value="<?php echo $application['id'] ?>"><?php echo $application['displayName'] ?></option>
		<?php endforeach; ?>
		</select>
		<label for="copy">Copy</label>
		<textarea name="copy"><?php if(isset($pagedata['copy'])) echo $pagedata['copy'] ?></textarea>
		<label for="menuShow">Show in menu:</label><input type="checkbox" name="menuShow" <?php if(isset($pagedata['menuShow']) && $pagedata['menuShow']) echo 'checked="checked"' ?>" >
		<input type="submit" name="submit" value="submit"/>
	</form>

<?php else: ?>
	<a href="?action=add">Add Page</a>

<?php endif; ?>


<?php echo $output; ?>