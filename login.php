<?php

$error = '';

if(isset($_POST['username']) && isset($_POST['password']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	
	require_once 'includes/db-connect.inc.php';
	$db = Database::ConnectDb();
	
	$email = $_POST['username'];
	$password = $_POST['password'];
	
	
	if ($email == "" || $password == "") {
		$error = "<span class='validation-error'>Not all fields were entered</span>";
	} else {
		$stmt = $db->prepare('SELECT UserEmail, UserPassword, UserId
								FROM note_users 
								WHERE UserEmail = :email 
								LIMIT 1'
							);
		$stmt->execute(array(':email' => $email));
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if($stmt->rowCount() == 0) {
			$error = "<span class='validation-error'>Username/Password invalid</span>";
		} else {
			$encrypted = $results[0]['UserPassword'];
			$userId = $results[0]['UserId'];
			if(password_verify($password, $encrypted)) {
				session_start();
				$_SESSION['user'] = $email;
				$_SESSION['userId'] = $userId;
				die(header('Location: index.php'));
			} else {
				$error = "<span class='validation-error'>Username/Password invalid</span>";
			}	
		}		
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
			<?php if($error !== '') echo $error; ?>
		</div>
		<button type="submit" name="login-button" class="btn btn-default">
			Submit
		</button>
	</form>
</div>

<?php include 'templates/footer.html'; ?>