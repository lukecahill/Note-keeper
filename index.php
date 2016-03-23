<html>
<head>
	<title>
		Notes
	</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
	<?php 
		include 'includes/db-connect.inc.php'; 
		$baseUrl = getcwd();
	?>
	<div class="container-fluid">
		<div class="jumbotron">
			<h1>
				Note Keeper.
			</h1>
		</div>
		
		<div class="col-sm-12">
			<button class="btn btn-success" id="new-note-button">
				<span class="glyphicon glyphicon-plus"></span>
				Add New Note
			</button>
		</div>
		
		<?php include $baseUrl . '/templates/new-note.php'; ?>
		
		<div class="col-sm-12">
			<div class="row">
				<div id="note-list">
				<div>
			</div>
		</div>
	</div>
	
	<?php include $baseUrl . '/templates/footer.html'; ?>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="js/script.js"></script>
</body>
</html>