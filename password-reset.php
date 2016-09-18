<?php require_once 'includes/PasswordReset.php'; ?>
<!DOCTYPE html>
<html>
<head>
	<title>
		Note Keeper
	</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid">
	<div class="jumbotron">
		<h1>
			<img src="images/note.png" class="note-logo"></img>
				Note Keeper.
		</h1>
	</div>
	
	<div class="col-sm-2">
	</div>
	
	<div class="col-sm-8 row">
		<h3>
			Forgotton Password Form
		</h3>
		<p>
			To reset your accounts password, please use the below form.
		</p>
        <p class="error">
            Please note that this is currently being implemented.
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
	</div>
	
	<div class="col-sm-2">
	</div>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="js/password-reset.js"></script>

<?php include 'templates/footer.html'; ?>