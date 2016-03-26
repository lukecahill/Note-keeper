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
				<span class="glyphicon glyphicon-comment">
				</span>
					Note Keeper.
			</h1>
		</div>
		
		<div class="col-sm-12 row">
		
			<?php include $baseUrl . '/templates/signin.php'; ?>
		
			<div class="col-sm-6">
				<button class="btn btn-success" id="new-note-button">
					<span class="glyphicon glyphicon-comment"></span>
					Add New Note
				</button>
			</div>
			
			<div class="col-sm-6 text-right">
				<button class="btn btn-default" id="refresh-button">
					<span class="glyphicon glyphicon-refresh"></span>
					Refresh
				</button>
				
				<button class="btn btn-default" id="show-all-notes-button">
					<span class="glyphicon glyphicon-search"></span>
					Show All Notes
				</button>
			</div>
		</div>
		
		<?php 
			include $baseUrl . '/templates/new-note.php';
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