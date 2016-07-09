<?php

require_once 'authentication.php';
require_once 'email-confirmation/send.php';

class Register extends Authentication {
	public $emailHash = '';
	public $passwordHash = '';
	public $error = '';

	function __construct($email, $password) {
		parent::__construct($email, $password);
	}

	function hashEmail() {
		$this->emailHash = md5($this->email);
		return $this->emailHash;
	}

	function hashPassword() {
		$this->passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
		return $this->passwordHash;
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
		$this->hashEmail();
		$this->hashPassword();
		$stmt = $this->db->prepare('INSERT INTO note_users (UserId, UserEmail, UserPassword) VALUES(:id, :email, :password);');
		$stmt->execute(array(':id' => $this->emailHash, ':email' => $this->email, ':password' => $this->passwordHash));
	}
	
	function sendConfirmation() {
		$confirm = new Email($this->email, $this->emailHash);
		$confirm->constructConfirmLink();
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
		$register->sendConfirmation();
	} else {
		$error = '<span class="validation-error">That email is already in use</span>';
	}
} else {
	$success = false; 
	$error = '';
}

?>