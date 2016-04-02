<?php

$success = false;
$error = '';

if(isset($_POST['email']) && isset($_POST['password']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	require_once 'includes/db-connect.inc.php';
	$db = ConnectDb();
	
	$email = $_POST['email'];
	$password = $_POST['password'];	
	$hash = password_hash($password, PASSWORD_DEFAULT);
	
	$check = $db->prepare('SELECT UserEmail FROM note_users WHERE UserEmail = :email LIMIT 1');
	$check->execute(array(':email' => $email));
	
	$emailHash = md5($email);
	
	if($check->rowCount() == 0) {
		$stmt = $db->prepare('INSERT INTO note_users (UserId, UserEmail, UserPassword) VALUES(:id, :email, :password);');
		$stmt->execute(array(':id' => $emailHash, ':email' => $email, ':password' => $hash));
		
		$success = true;
	} else {
		$error = '<span class="validation-error">That email is already in use</span>';
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>
		Notes
	</title>
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

<div class="col-sm-12 row">
<?php if($success === false) { ?>
	<h3>
		Register
	</h3>
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
			<?php if($error !== '') echo $error; ?>
		</div>
		<div class="form-group">
			<label for="user-password">
				Password
			</label>
			<input type="password" name="password" class="form-control" id="user-password" placeholder="Password">
		</div>
		<button type="submit" class="btn btn-default">
			Submit
		</button>
	</form>
<?php } else { ?>
	<h3>
		Register
	</h3>
	
	<p>
		Registration complete. Please <a href="login.php">login</a>.
	</p>
<?php } ?>

</div>

<?php include 'templates/footer.html'; ?>