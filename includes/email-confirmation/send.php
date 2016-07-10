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

		$url = $_SERVER['SERVER_NAME'];
		$link = 'http://' . $url . '/notes/includes/email-confirmation/confirm.php?hash=' . $this->userId . '&user=' . $this->userId;
		$this->message = 'Please follow this link to confirm your account <a href="' . $link . '">' . $link . '</a>';
		$this->sendEmail();
	}

	function sendEmail() {
		mail($this->email, $this->subject, $this->message);
	}
}

?>