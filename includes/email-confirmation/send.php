<?php

require_once '../db-connect.inc.php';

class Email {
	$db = null;
	public $email = '';
	public $from = 'luke@lcahill.co.uk';
	public $subject = 'Note Keeper - please confirm your email.';
	public $message = '';

	function __construct($email) {
		$this->email = $email;
		require_once 'db-connect.inc.php';
		$this->db = Database::ConnectDb();
	}

	function constructConfirmLink() {
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		$confirm = md5($timestamp);

		$stmt = $this->db->prepare("UPDATE note_users SET EmailConfirmation = :confirm WHERE UserId = :userId");
		$stmt->execute(array(':confirm' => $confirm));

		$this->email = 'test@example.com';
		// TODO : actually generate the below link.
		$this->message = 'Please follow this link to confirm your account <a href=""></a>';
		$this->sendEmail();
		// TODO : This is too much work for right now so I will have to come back to this. 
		// Basic outline of what is needed:
		// The above link needs to be generated with a random unique link each time - hash the email and time
		// The account will need to be check that it has been confirmed when logging in
		// There will have to be a page where the user can go to which will confirm the account.
		// The above page can be automatic or manual entry - GET in request with automatic - this doesn't matter at the current stage
		// Actually create this object and enter the email where the link will be sent too.
		// Update the database to allow the user to use the account after confirming
		// There is probably more to this but I'm going to bed now - good luck future Luke.
	}

	function sendEmail() {
		mail($this->email, $this->from, $this->subject, $this->message);
	}
}

?>