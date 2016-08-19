<?php include 'templates/header.php'; ?>
	
<div class="col-md-12 row">
	<div class="row col-md-12">
		<div class="col-sm-6">
		
			<button class="btn btn-success" id="new-note-button">
				<span class="glyphicon glyphicon-comment"></span>
				Add new note
			</button>
			
			<button class="btn btn-default" id="complete-notes-button">
				<span class="glyphicon glyphicon-asterisk"></span>
			</button>
		</div>

		<div class="col-sm-6 text-right mobile-text-left">
			<button class="btn btn-default" id="refresh-button" title="Refresh the note list">
				<span class="glyphicon glyphicon-refresh"></span>
				Refresh list
			</button>
			
			<button class="btn btn-default" id="show-search-button" title="Search for a note via the title">
				<span class="glyphicon glyphicon-search"></span>
			</button>
			
			<button class="btn btn-default" id="show-tag-chooser-button" title="Show a list of tags which are available to show all notes with that tag.">
				<span class="glyphicon glyphicon-tag"></span>
			</button>
		</div>
	</div>
	<?php
		include 'templates/tag-chooser.html';
		include 'templates/search.html';
	?>
</div>

<?php 
	include 'templates/new-note.html';
?>

<div class="col-sm-12 row">
	<div class="row">
		<div id="note-list">
		</div>
	</div>
</div>

<?php include 'templates/modals/edit-note.html'; ?>
<?php echo '<script>var userId = "' . $_SESSION['userId'] . '"; </script>'; // use this to echo the session user ID for the JS to use ?> 
<?php echo '<script>var auth = "' . $_SESSION['authentication'] . '"; </script>'; ?> 
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="js/script.js"></script>

<?php include 'templates/footer.html'; ?>