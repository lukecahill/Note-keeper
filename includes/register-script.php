<?php

include 'email-confirmation/SendEmail.php';

spl_autoload_register(function ($class_name) {
	include $class_name . '.php';
});

/**
* Register is used to register the user in the database. 
* Inherits from the base class Authentication
*
* @package  Note Keeper
* @author   Luke Cahill
* @access   public
*/
class Register extends Authentication {
	public $emailHash = '';
	public $passwordHash = '';
	public $confirm = '';
	public $error = '';
	
	/**  
	* Construct the register class
	*
	* @param string $email The users email which was used to log in
	* @param string $password The password the user entered when logging in
	* @param string $confirm The confirmation password entered by the user.
	*/
	function __construct($email, $password, $confirm) {
		parent::__construct($email, $password);
		$this->confirm = $confirm;
	}
	
	/**  
	* Verify that the user entered an email, password and confirmation password.
	*
	* @return bool Depending on if the passwords entered were not empty, and the user entered an email
	*/
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

		if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$this->error = '<span class="validation-error">Please enter a valid email address!</span>';
			return false;
		}
		
		return true;
	}

	/**  
	* Create an MD5 hash of the users ID
	*
	* @return string MD5 hash of the users email for thier ID
	*/
	function hashEmail() {
		$this->emailHash = md5($this->email);
		return $this->emailHash;
	}

	/**  
	* Uses PHP 5.5+ password_hash() function which hashes the users password
	*
	* @return string Hash of the users password
	*/
	function hashPassword() {
		$this->passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
		return $this->passwordHash;
	}

	/**  
	* Check the database to see if a user has already been created with the entered email.
	*
	* @return bool True if the email doesn't exit and false if the email already exists.
	*/
	function checkExists() {
		$check = $this->db->prepare('SELECT UserEmail FROM note_users WHERE UserEmail = :email LIMIT 1');
		$check->execute(array(':email' => $this->email));
		if($check->rowCount() == 0) {
			return true;
		} else {
			return false;
		}
	}

	/**  
	* Add the user to the database with their hashed password, email, and ID
	*
	* @return bool True if the statement is successful, false if not.
	*/
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
	
	/**  
	* Creates a record for the users default preferences in the note_user_preferences table
	*
	* @return void
	*/
	function createUserPreferences() {
		$stmt = $this->db->prepare('INSERT INTO note_user_preferences (UserId) VALUES (:id)');
		$stmt->execute(array(':id' => $this->emailHash));
	}
	
	/**  
	* Creates a new emailer to email the user their confirmation link so that they can 
	* 	confirm their email address. 
	*
	* @return void
	*/
	function sendConfirmation() {
		$confirm = new SendEmail($this->email, $this->emailHash, $this->db);
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
	
	// find a better way of doing this. 
	if($register->checkExists()) {
		if($register->addUser()) {
			$register->sendConfirmation();
			$register->createUserPreferences();
			$success = true;
		} else {
			$error = '<span class="validation-error">There was an error when attempting to create your account. Please contact the server host.</span>';
		}
	} else {
		$error = '<span class="validation-error">That email is already in use</span>';
	}
} else {
	$success = false; 
	$error = '';
}

?>