<?php
class Email {
	public $db = null;
	public $email = '';
	public $from = 'luke@lcahill.co.uk';
	public $subject = 'Note Keeper - please confirm your email.';
	public $message = '';
	public $userId = '';
	public $headers = 'From: luke@lcahill.co.uk' . "\r\n" . 'Reply-To: luke@lcahill.co.uk' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

	function __construct($email, $userId, $db) {
		$this->email = $email;
		$this->userId = $userId;
		$this->db = $db;
	}

	function constructConfirmLink() {
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		$confirm = md5($timestamp);

		$stmt = $this->db->prepare("UPDATE note_users SET EmailConfirmation = :confirm WHERE UserId = :userId");
		$stmt->execute(array(':confirm' => $confirm, ':userId' => $this->userId));

		$url = $_SERVER['SERVER_NAME'];
		$link = 'http://' . $url . '/notes/confirm.php?hash=' . $confirm . '&user=' . $this->userId;
		$this->message = 'Please follow this link to confirm your account ' . $link;
		$this->sendEmail();
	}

	function sendEmail() {
		mail($this->email, $this->subject, $this->message, $this->headers);
	}
}

?>