<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title; ?></title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.1/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="/themes/default/javascript/jquery.ui.touch-punch.min.js"></script>

	<link rel="stylesheet" type="text/css" href="/themes/default/css/style.css"/>
	<link rel="shortcut icon" href="/themes/default/graphics/favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
</head>

<body>

	<?php include '/themes/default/html/menu.php'; ?>
	
	<div id="main">
	
	<img id="logo" src="/themes/default/graphics/logo-small.png"/>
	
	
	<?php echo $applicationOutput; ?>
	
	</div>
	
	<div id="footer">
		<?php $time = time(); $year= date("Y",$time); echo "&#x00A9; ".$year ?> - <a href="http://jred.co.uk">jred.co.uk</a><br/> 
	</div>
</body>
</html>