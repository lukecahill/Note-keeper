<?php 
	$url = $_SERVER['REQUEST_URI']; 
?>

<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9" aria-expanded="true">
				<span class="sr-only">
					Toggle navigation
				</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">
				Note Keeper
			</a>
		</div>
		<div class="navbar-collapse collapse in" id="bs-example-navbar-collapse-9" aria-expanded="true"> 
			<ul class="nav navbar-nav">
				<li <?php if(strpos($url, 'index.php')) echo 'class="active" ' ?>>
					<a href="index.php">
						Home
					</a>
				</li>
				<li <?php if(strpos($url, 'options.php')) echo 'class="active" ' ?>>
					<a href="options.php">
						Options
					</a>
				</li>
				<li class="pull right">
					<a href="includes/logout.php">
						Logout
					</a>
				</li> 
			</ul>
		</div>
	</div>
</nav>