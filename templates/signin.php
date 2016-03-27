<div class="col-sm-12 text-right">

<?php if(isset($_SESSION['user'])) { ?>
	<span class="logout-link">
		<a href="includes/logout.php">
			Logout
		</a>
	</span>
<?php } else { ?>
	<span class="login-link">
		<a href="login.php">
			Log in
		</a>
	</span>
	<span class="register-link">
		<a href="register.php">
			Register
		</a>
	</span>
<?php } ?>

</div>