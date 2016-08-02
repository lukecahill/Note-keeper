<?php include 'templates/header.php'; ?>
	
<div class="col-sm-12 row">
	<div class="col-sm-12">
		<h2>
			User Options 
		</h2>
		
		<p>
			<form action="includes/logout.php">
				<button class="btn btn-default pull-right">
					Logout
				</button>
			</form>
		</p>
		
		<p>
			Modify the available options to your preferences. Click on the header to show the available options.  
		</p>
		
		<p>
			<b>
				Email:
			</b>
			<?php echo $_SESSION['user']; ?>
		</p>
		
		<p>
			<b>
				Total number of active notes:
			</b>
			<span id="total_notes">
			</span>
		</p>

		<?php include 'templates/color-select.html'; ?>

		<?php include 'templates/note-order.html'; ?>

		<?php include 'templates/search-options.html'; ?>

		<?php include 'templates/password.php'; ?>

		<?php include 'templates/recent-ips.html'; ?>
	</div>
</div>

<?php echo '<script>var userId = "' . $_SESSION['userId'] . '"; </script>'; // use this to echo the session user ID for the JS to use ?> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="js/user-script.js"></script>

<?php include 'templates/footer.html'; ?>