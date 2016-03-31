<?php include 'templates/header.php'; ?>
	
<div class="col-sm-12 row">

	<?php include 'templates/signin.php'; ?>

	<div class="col-sm-7">
		<button class="btn btn-success" id="new-note-button">
			<span class="glyphicon glyphicon-comment"></span>
			Add New Note
		</button>
		
		<button class="btn btn-default" id="complete-notes-button">
			<span class="glyphicon glyphicon-asterisk"></span>
			Show Completed Notes
		</button>
	</div>
	
	<div class="col-sm-5 text-right mobile-text-left">
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
	include 'templates/new-note.php';
?>

<div class="col-sm-12 row">
	<div class="row">
		<div id="note-list">
		</div>
	</div>
</div>

<?php include 'templates/modals/edit-note.html'; ?>
<?php echo '<script>var userId = "' . $_SESSION['userId'] . '"; </script>'; // use this to echo the session user ID for the JS to use ?> 
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="js/script.js"></script>

<?php include 'templates/footer.html'; ?>