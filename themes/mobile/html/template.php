<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title; ?></title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript" ></script>
	<link rel="apple-touch-icon" href="/themes/default/graphics/appbutton.png"/>
	<link rel="apple-touch-icon-precomposed" href="/themes/default/graphics/appbutton.png"/>
	<link rel="stylesheet" type="text/css" href="/themes/mobile/css/mobile.css"/>
	<link rel="stylesheet" type="text/css" href="/themes/mobile/css/debug.css"/>
	<script src="/themes/mobile/javascript/dropdown.js" type="text/javascript"></script>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="viewport" content="width=device-width" />
	<?php if(isset($_GET['debug'])): ?>
	<script src="/themes/mobile/javascript/debug.js" type="text/javascript"></script>
	<?php endif; ?>
	<link rel="shortcut icon" href="/themes/default/graphics/favicon.png" />
</head>

<body>
	<span id="bgtex"></span> 

	<?php include '/themes/mobile/html/menu.php'; ?>
	
	<div id="main">
	
	<?php echo $applicationOutput; ?>
	
	</div>
	
	<span id="footer">
		<?php $time = time () ; $year= date("Y",$time); echo "&#x00A9; " . $year . " - electronoodles.co.uk";?><br/> 
		All trademarks are the property of their respective owners. 
		<a href="http://steampowered.com">Powered by Steam</a>
	</span>
	
	<script src="/themes/mobile/javascript/analytics.js"></script>
</body>
</html>