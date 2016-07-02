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