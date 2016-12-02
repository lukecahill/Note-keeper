<?php require_once 'includes/PasswordReset.php'; ?>

<?php require_once 'templates/unregistered-header.html'; ?>
	
	<div class="col-sm-2">
	</div>
	
	<div class="col-sm-8 row">
	<?php 
	if(!isset($_POST['password-reset-btn'])) {
	?>
		<h3>
			Forgotton Password Form
		</h3>
		<p>
			To reset your accounts password, please use the below form.
		</p>
		<p>
			Here by accident? <a href="login.php">Login here</a>.
		</p>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<div class="form-group">
				<label for="user-email">
					Email address
				</label>
				<input type="email" name="email" class="form-control" id="user-email" placeholder="Email">
			</div>
			<button type="submit" id="password-reset-btn" class="btn btn-default">
				Submit
			</button>
		</form>
	<?php
	} else { 
	?>
		<h3>
			Your password reset link has been sent.
		</h3>
		<p>
			Please follow the link provided in your email. 
		</p>
	<?php 
	} 
	?>
	</div>
	
	<div class="col-sm-2">
	</div>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="js/common.js"></script>
	<script src="js/password-reset.js"></script>

<?php include 'templates/footer.html'; ?>