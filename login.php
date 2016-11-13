<?php require_once 'includes/login-script.php'; ?>

<?php require_once 'templates/unregistered-header.html'; ?>

	<div class="col-sm-2">
	</div>
	<div class="col-sm-8 row">
		<h2>
			Login
		</h2>
		<h3>
			Please log in to view your notes.
		</h3>
		<p>
			Don't have an account? <a href="register.php">Register here!</a>
		</p>
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<div class="form-group">
				<label for="user-email">
					Email address
				</label>
				<input type="email" name="username" class="form-control" id="user-email" placeholder="Email">
			</div>
			<div class="form-group">
				<label for="user-password">
					Password
				</label>
				<input type="password" name="password" class="form-control" id="user-password" placeholder="Password">
				<?php if(isset($login)) { if($login->error !== '') echo $login->error; } ?>
			</div>
			<button type="submit" name="login-button" class="btn btn-default">
				Submit
			</button>
		</form>
		<form method="POST" action="password-reset.php">
			<div class="password_reset_container">
				<button class="password_reset" name="password_reset" type="submit">
					Forgot your password
				</button>
			</div>
		</form>
	</div>
	<div class="col-sm-2">
	</div>

<?php include 'templates/footer.html'; ?>