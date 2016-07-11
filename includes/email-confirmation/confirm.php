<?php

class ConfirmEmail {
	public $hash = '';
	public $id = '';
	public $db = null;
	
	function __construct($hash, $id) {
		require_once 'includes/db-connect.inc.php';
		$this->db = Database::ConnectDb();
		$this->id = $id;
		$this->hash = $hash;
	}
	
	function getId() {
		$stmt = $this->db->prepare('SELECT UserId FROM note_users WHERE UserId = :id AND EmailConfirmation = :hash');
		$stmt->execute(array(':id' => $this->id, ':hash' => $this->hash));
		
		if($stmt->rowCount() != 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function checkHash() {
		$stmt = $this->db->prepare('UPDATE note_users SET Active = 1 WHERE UserId = :id');
		$stmt->execute(array(':id' => $this->id));
		
		if($stmt->rowCount() == 0) {
			return true;
		} else {
			return false;
		}
	}
}

if(isset($_GET['hash']) && !empty($_GET['hash']) && isset($_GET['user']) && !empty($_GET['user'])) {
	
	$confirm = new ConfirmEmail($_GET['hash'], $_GET['user']);
	
	if($confirm->getId()) {
		if($confirm->checkHash()) {
			echo 'Your email has been confirmed. Please now <a href="login.php">log in</a>.';
		}
	} else {
		echo 'The user ID or the hash which was entered was incorrect. Please check your email.';
	}
	
} else {
	echo 'No email found are you sure you are meant to be here?';
}

?>