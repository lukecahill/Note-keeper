<?php 
	require_once 'includes/db-connect.inc.php'; 
	$baseUrl = $_SERVER['DOCUMENT_ROOT'] . '/notes';
?>

<?php include 'templates/header.php'; ?>
	
<div class="col-sm-12 row">

	<?php include $baseUrl . '/templates/signin.php'; ?>

	<div class="col-sm-6">
		<button class="btn btn-success" id="new-note-button">
			<span class="glyphicon glyphicon-comment"></span>
			Add New Note
		</button>
		
		<button class="btn btn-default" id="complete-notes-button">
			<span class="glyphicon glyphicon-asterisk"></span>
			Show Completed Notes
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

<?php echo '<script>var userId = 1;</script>'; // use this to echo the session user ID for the JS to use?> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="js/script.js"></script>

<?php include $baseUrl . '/templates/footer.html'; ?>