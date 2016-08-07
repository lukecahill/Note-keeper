<?php

require_once 'authentication.php';
require_once 'email-confirmation/send.php';

class Register extends Authentication {
	public $emailHash = '';
	public $passwordHash = '';
	public $confirm = '';
	public $error = '';

	function __construct($email, $password, $confirm) {
		parent::__construct($email, $password);
		$this->confirm = $confirm;
	}
	
	function verify() {
		if(empty($this->email)) {
			$this->error = '<span class="validation-error">Please enter your email!</span>';
			return false;
		}
		
		if(empty($this->password)) {
			$this->error = '<span class="validation-error">Please enter a password!</span>';
			return false;
		}
		
		if(empty($this->confirm)) {
			$this->error = '<span class="validation-error">Please confirm your password!</span>';
			return false;
		}
		
		return true;
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
		
		if($stmt) {
			return true;
		} else {
			return false;
		}
	}
	
	function createUserPreferences() {
		$stmt = $this->db->prepare('INSERT INTO user_preferences (UserId) VALUES (:id)');
		$stmt->execute(array(':id' => $this->emailHash));
	}
	
	function sendConfirmation() {
		$confirm = new Email($this->email, $this->emailHash, $this->db);
		$confirm->constructConfirmLink();
	}
}

if(isset($_POST['email']) && isset($_POST['password']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	$email = $_POST['email'];
	$password = $_POST['password'];
	$confirm = $_POST['confirm-password'];
	$error = '';
	$success = false;

	$register = new Register($email, $password, $confirm);
	if(!$register->verify()) {
		$error = $register->error;
		return;
	}
	
	if($register->checkExists()) {
		if($register->addUser()) {
			$register->sendConfirmation();
			$register->createUserPreferences();
			$success = true;
		}
	} else {
		$error = '<span class="validation-error">That email is already in use</span>';
	}
} else {
	$success = false; 
	$error = '';
}

?>