<?php


class Email {
	public $db = null;
	public $email = '';
	public $from = 'luke@lcahill.co.uk';
	public $subject = 'Note Keeper - please confirm your email.';
	public $message = '';
	public $userId = '';

	function __construct($email, $userId) {
		$this->email = $email;
		$this->userId = $userId;
		require_once '../db-connect.inc.php';
		$this->db = Database::ConnectDb();
	}

	function constructConfirmLink() {
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		$confirm = md5($timestamp);

		$stmt = $this->db->prepare("UPDATE note_users SET EmailConfirmation = :confirm WHERE UserId = :userId");
		$stmt->execute(array(':confirm' => $confirm, ':userId' => $this->userId));

		// TODO : actually generate the below link.
		$link = 'http://localhost/notes/includes/email-confirmation/confirm.php?hash=' . $this->userId . '&user=' . $this->userId;
		$this->message = 'Please follow this link to confirm your account <a href="' . $link . '">' . $link . '</a>';
		$this->sendEmail();
		// TODO : This is too much work for right now so I will have to come back to this. 
		// Basic outline of what is needed:
		// The above link needs to be generated with a random unique link each time - hash the email and time - done
		// The account will need to be check that it has been confirmed when logging in
		// There will have to be a page where the user can go to which will confirm the account.
		// The above page can be automatic or manual entry - GET in request with automatic - this doesn't matter at the current stage
		// Actually create this object and enter the email where the link will be sent too.
		// Update the database to allow the user to use the account after confirming
		// There is probably more to this but I'm going to bed now - good luck future Luke.
		
		
		// http://localhost/notes/includes/email-confirmation/confirm.php?hash=123&user=lmc@outlook.com - sample generated link
	}

	function sendEmail() {
		mail($this->email, $this->subject, $this->message);
	}
}

?>