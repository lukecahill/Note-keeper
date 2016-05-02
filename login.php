<?php

require_once 'authentication.php';

class Login extends Authentication {
	public $error = '';

	function __construct($email, $password) {
		parent::__construct($email, $password);
	}

	function checkValid() {
		if($this->email == '' || $this->password == '') {
			$this->error = "<span class='validation-error'>Not all fields were entered</span>";
			return false;
		} else {
			return true;
		}
	}

	function verify() {
		$stmt = $this->db->prepare('SELECT UserEmail, UserPassword, UserId
								FROM note_users 
								WHERE UserEmail = :email 
								LIMIT 1'
							);
		$stmt->execute(array(':email' => $this->email));
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(count($results) == 0) {
			$this->error = "<span class='validation-error'>Username/Password invalid</span>";
		} else {
			$encrypted = $results[0]['UserPassword'];
			$userId = $results[0]['UserId'];
			if(password_verify($this->password, $encrypted)) {
				session_start();
				$_SESSION['user'] = $this->email;
				$_SESSION['userId'] = $userId;
				die(header('Location: index.php'));
			} else {
				$this->error = "<span class='validation-error'>Username/Password invalid</span>";
			}	
		}
	}
}

if(isset($_POST['username']) && isset($_POST['password']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$email = $_POST['username'];
	$password = $_POST['password'];
	$login = new Login($email, $password);
	
	if($login->checkValid()) {
		$results = $login->verify();
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
			<?php if(isset($login)) { if($login->error !== '') echo $login->error; } ?>
		</div>
		<button type="submit" name="login-button" class="btn btn-default">
			Submit
		</button>
	</form>
</div>

<?php include 'templates/footer.html'; ?>