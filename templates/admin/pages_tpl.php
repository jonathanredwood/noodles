<h1>Page management</h1>

<?php if(isset($_GET['pageid']) && $_GET['pageid'] != 0): ?>
<a href="?pageid=<?php echo $parentid ?>">Back</a>
<?php endif; ?>

<?php if(isset($editor) && $editor): ?>

<form method="post" action="?pageid=<?php echo $_GET['pageid'] ?>">
	<label for="title">Title</label><input name="title" type="text" value="<?php echo $pagedata['title'] ?>">
	<label for="url">URL</label><input name="url" type="text" value="<?php echo $pagedata['url'] ?>">
	<label for="application">Application</label>
	<select name="application">
	<?php foreach($applications as $application): ?>
		 <option <?php if($application['id'] == $pagedata['application']) echo 'selected' ?> value="<?php echo $application['id'] ?>"><?php echo $application['displayName'] ?></option>
	<?php endforeach; ?>
	</select>
	<label for="copy">Copy</label>
	<textarea name="copy"><?php echo $pagedata['copy'] ?></textarea>
	<input type="submit">
</form>
<?php endif; ?>


<?php echo $output; ?>