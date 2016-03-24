<html>
<head>
	<title>
		Notes
	</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
	<?php 
		require_once 'includes/db-connect.inc.php'; 
		$baseUrl = getcwd();
	?>
	<div class="container-fluid">
		<div class="jumbotron">
			<h1>
				Note Keeper.
			</h1>
		</div>
		
		<div class="col-sm-12 row">
			<button class="btn btn-success" id="new-note-button">
				<span class="glyphicon glyphicon-plus"></span>
				Add New Note
			</button>
			<button class="btn btn-success" id="show-new-tag-button">
				<span class="glyphicon glyphicon-tag"></span>
				Add New Tag
			</button>
		</div>
		
		<div class="col-sm-12 row">
			<button class="btn btn-success" id="show-all-notes-button">
				<span class="glyphicon glyphicon-search"></span>
				Show All Notes
			</button>
		</div>
		
		<?php 
			include $baseUrl . '/templates/new-note.php';
			include $baseUrl . '/templates/new-tag.php'; 
		?>
		
		<div class="col-sm-12 row">
			<div class="row">
				<div id="note-list">
				</div>
			</div>
		</div>
	</div>
	
	<?php include $baseUrl . '/templates/footer.html'; ?>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<script src="js/script.js"></script>
</body>
</html>