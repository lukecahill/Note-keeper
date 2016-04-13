<?php

require_once 'authentication.php';

class Register extends Authentication {
	private $emailHash = '';
	private $passwordHash = '';
	private $error = '';

	function __construct($email, $password) {
		parent::__construct($email, $password);
	}

	function hashEmail() {
		$this->emailHash = md5($email);
		return $this->emailHash;
	}

	function hashPassword() {
		$this->hashPassword = password_hash($password, PASSWORD_DEFAULT);
		return $this->hashPassword;
	}

	function checkExists() {
		$check = $this->db->prepare('SELECT UserEmail FROM note_users WHERE UserEmail = :email LIMIT 1');
		$check->execute(array(':email' => $this->email));
		if($check->rowCount() == 0) {
			return true;
		} else {
			return false;
		}
	}

	function addUser() {
		$stmt = $this->db->prepare('INSERT INTO note_users (UserId, UserEmail, UserPassword) VALUES(:id, :email, :password);');
		$stmt->execute(array(':id' => $this->emailHash, ':email' => $this->email, ':password' => $this->passwordHash));
	}
}

if(isset($_POST['email']) && isset($_POST['password']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	$email = $_POST['email'];
	$password = $_POST['password'];	
	$error = '';
	$success = false;

	$register = new Register($email, $password);
	if($register->checkExists()) {
		$register->addUser();
		$success = true;
	} else {
		$error = '<span class="validation-error">That email is already in use</span>';
	}
} else {
	$success = false; 
	$error = '';
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