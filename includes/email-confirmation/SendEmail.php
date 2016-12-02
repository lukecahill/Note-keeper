<?php
class SendEmail {
	public $db = null;
	public $email = '';
	public $from = 'luke@lcahill.co.uk';
	public $subject = '';
	public $message = '';
	public $userId = '';
	public $headers = "From: luke@lcahill.co.uk \r\n Reply-To: luke@lcahill.co.uk \r\n";
	public $reset = false;

	function __construct($email, $userId, $db, $reset = false) {
		$this->email = $email;
		$this->userId = $userId;
		$this->db = $db;
		$this->reset = $reset;
	}

	function constructConfirmLink() {
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		$confirm = md5($timestamp);
		$link = '';

		$stmt = $this->db->prepare("UPDATE note_users SET EmailConfirmation = :confirm WHERE UserId = :userId");
		$stmt->execute(array(':confirm' => $confirm, ':userId' => $this->userId));

		$url = $_SERVER['SERVER_NAME'];
		if($this->reset) {
			$link = 'http://' . $url . '/notes/reset.php?hash=' . $confirm . '&user=' . $this->userId;
			$this->message = 'Please follow this link to reset your password ' . $link . '\n\nIf you did not request this then you can ignore this email.';
			$this->subject = 'Note Keeper - reset your password.';
		} else {
			$link = 'http://' . $url . '/notes/confirm.php?hash=' . $confirm . '&user=' . $this->userId;
			$this->message = 'Please follow this link to confirm your account ' . $link;
			$this->subject = 'Note Keeper - please confirm your email.';
		}
		
		$this->sendEmail();
	}

	function sendEmail() {
		mail($this->email, $this->subject, $this->message, $this->headers);
	}
}

?>