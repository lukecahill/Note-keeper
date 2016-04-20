<?php include 'templates/header.php'; ?>
	
<div class="col-sm-12 row">

	<?php include 'templates/signin.php'; ?>
	<div class="col-sm-12">
		<h2>
			User Options 
		</h2>
		
		<p>
			Modify your options to your preferences. 
		</p>
		
		<p>
			<b>
				Email:
			</b>
			<?php echo $_SESSION['user']; ?>
		</p>
		
		<p>
			<div class="form-group">
				<label for="user-tag-color">
					Hexadecimal color code - Enter a color for your tags (as hexidecimal code e.g. #FF0000):
				</label>
				<input type="text" id="user-tag-color" class="form-control">
			</div>
			<button class="btn btn-success" id="tag-color-button">
				<span class="glyphicon glyphicon-plus"></span>
				Change Color
			</button>
			
			<?php include 'templates/password.php'; ?>
		</p>
	</div>
</div>

<?php echo '<script>var userId = "' . $_SESSION['userId'] . '"; </script>'; // use this to echo the session user ID for the JS to use ?> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="js/user-script.min.js"></script>

<?php include 'templates/footer.html'; ?>