<?php require_once 'includes/register-script.php'; ?>

<?php require_once 'templates/unregistered-header.html'; ?>
	
	<div class="col-sm-2">
	</div>
	
	<div class="col-sm-8 row">
	<?php if($success === false) { ?>
		<h3>
			Register
		</h3>
		<?php if($error !== '') echo $error; ?>
		<p>
			To create an account please enter your email address and a password.
		</p>
		<p>
			Already have an account? Please <a href="login.php">login here</a>.
		</p>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<div class="form-group">
				<label for="user-email">
					Email address
				</label>
				<input type="email" name="email" class="form-control" id="user-email" placeholder="Email">
			</div>
			<div class="form-group">
				<label for="user-password">
					Password
				</label>
				<input type="password" name="password" class="form-control" id="user-password" placeholder="Password">
			</div>
			<div class="form-group">
				<label for="confirm-user-password">
					Confirm Password
				</label>
				<input type="password" name="confirm-password" class="form-control" id="confirm-user-password" placeholder="Password confirmation">
			</div>
			<button type="submit" id="register-btn" class="btn btn-default">
				Submit
			</button>
		</form>
	<?php } else { ?>
		<h3>
			Register
		</h3>
		
		<p>
			Registration complete. Please now check your email to your email address before logging in.
			If you have already confirmed your email then please <a href="login.php">login</a>.
		</p>
	<?php } ?>

	</div>
	
	<div class="col-sm-2">
	</div>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="js/register.js"></script>

<?php include 'templates/footer.html'; ?>