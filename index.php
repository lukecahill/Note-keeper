<html>
<head>
	<title>
		Notes
	</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
	<?php include 'includes/db-connect.inc.php'; ?>
	<?php $array = array('blue', 'white'); echo serialize($array); ?>
	
	<button id="get" class="btn btn-primary">
		Get Notes
	</button>
	<div id="demo">
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="js/script.js"></script>
</body>
</html>