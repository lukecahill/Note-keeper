<?php

spl_autoload_register(function ($class_name) {
	include 'email-confirmation/' . $class_name . '.php';
});

/**
* PasswordReset is used to reset the users password in the database.
*
* @package  Note Keeper
* @author   Luke Cahill
* @access   public
*/
class PasswordReset {
    public $email = '';
    public $db = null;
	public $confirm = '';
	public $error = '';
	
	/**  
	* Construct the PasswordReset class
	*/
	function __construct($email) {
        require_once 'db-connect.inc.php';
        $this->email = $email;
        $this->db = Database::ConnectDb();
     }
	
	/**  
	* Verify that the user entered an email
	*
	* @return bool Depending on if the email entered were not empty
	*/
	function verify() {
		if(empty($this->email)) {
			$this->error = '<span class="validation-error">Please enter your email!</span>';
			return false;
		}
		
		return true;
	}

	/**  
	* Check the database to see if a user has already been created with the entered email.
    * Note to self: Is this a security risk?
	*
	* @return bool True if the email doesn't exit and false if the email already exists.
	*/
	function checkExists() {
		$check = $this->db->prepare('SELECT UserEmail FROM note_users WHERE UserEmail = :email LIMIT 1');
		$check->execute(array(':email' => $this->email));
		if($check->rowCount() === 1) {
			return true;
		} else {
			return false;
		}
	}
    
	/**  
	* Creates a new emailer to email the user their password reset link.
	*
	* @return void
	*/
	function sendResetConfirmation() {
		$hashEmail = md5($this->email); 
		$confirm = new Email($this->email, $hashEmail, $this->db, true); 
		$confirm->constructConfirmLink();
	}
}

if(isset($_POST['email']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	$email = $_POST['email'];
	$error = '';
	$success = false;

	$reset = new PasswordReset($email);
	if(!$reset->verify()) {
		$error = $reset->error;
		return;
	}
	
	if($reset->checkExists()) {
		$reset->sendResetConfirmation();
		$success = true;
	} else {
		$error = '<span class="validation-error">Could not find that email address.</span>';
	}
} else {
	$success = false; 
	$error = '';
}

?>