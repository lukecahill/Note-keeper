<div class="col-sm-12 text-right site-menu">

<?php if(isset($_SESSION['user'])) { ?>
	<?php 
		$url = $_SERVER['REQUEST_URI']; 
		if(!strpos($url, 'options.php')) { 
	?>
		<span class="options-link">
			<a href="options.php">
				Options
			</a>
		</span>	
	<?php
		} else {
	?>
		<span class="index-link">
			<a href="index.php">
				Home
			</a>
		</span>	
	<?php } ?>
	
	<span class="divider">
		|
	</span>
	
	<span class="logout-link">
		<a href="includes/logout.php">
			Logout
		</a>
	</span>
<?php } else { ?>
	<span class="register-link">
		<a href="register.php">
			Register
		</a>
	</span>
	
	<span>
		|
	</span>
	
	<span class="login-link">
		<a href="login.php">
			Log in
		</a>
	</span>
<?php } ?>

</div>