<?php $url = $_SERVER['REQUEST_URI']; ?>

<nav class="navbar navbar-inverse navigation-bar">
	<div class="container-fluid navbar-container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9" aria-expanded="true">
				<span class="sr-only">
					Toggle navigation
				</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">
				Note Keeper
			</a>
		</div>
		<div class="navbar-collapse collapse" id="bs-example-navbar-collapse-9" aria-expanded="true"> 
			<ul class="nav navbar-nav">
				<li <?php if(strpos($url, 'index.php') || !strpos($url, 'account.php')) echo 'class="active" ' ?>>
					<a href="index.php">
						Home
					</a>
				</li>
				<li <?php if(strpos($url, 'account.php')) echo 'class="active" ' ?>>
					<a href="account.php">
						Account
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>