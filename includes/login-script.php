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
		$stmt = $this->db->prepare('SELECT UserEmail, UserPassword, UserId, Active
								FROM note_users 
								WHERE UserEmail = :email 
								LIMIT 1'
							);
		$stmt->execute(array(':email' => $this->email));
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(count($results) == 0) {
			$this->error = "<span class='validation-error'>Username/Password invalid</span>";
		} else if($results[0]['Active'] == 0) {
			$this->error = "<span class='validation-error'>This email address has not been confimred! Please check your email and follow the confirmation link.</span>";
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