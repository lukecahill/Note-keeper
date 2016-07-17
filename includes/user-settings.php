<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	$user = new UserSettings($_POST['action']);
	
	if($user->method === 'get-settings') {
		$user->getSettings();
	} else if($user->method === 'set-tag-color') {
		$user->setTagColor();
	} else if($user->method === 'set-note-order') {
		$user->setNoteOrder();
	}

} else {
	echo 'No direct access';
}

class UserSettings {
	public $method = '';
	public $id = 0;
	public $color = '';
	public $order = '';
	public $db = null;
	
	function __construct($method) {
		$this->method = $method;
		require_once 'db-connect.inc.php';
		$this->db = Database::ConnectDb();
	}
	
	function setTagColor() {
		if($_POST['id'] !== 0) {
			$this->color = $_POST['color'];
			$this->id = $_POST['id'];
			
			$stmt = $this->db->prepare('UPDATE user_preferences SET TagColor = :color WHERE UserId = :id ');
			$stmt->execute(array( ':color' => $this->color, ':id' => $this->id));
		}
	}
	
	function setNoteOrder() {
		if($_POST['id'] !== 0) {
			$this->order = $_POST['order'];
			$this->id = $_POST['id'];
			
			$stmt = $this->db->prepare('UPDATE user_preferences SET NoteOrder = :order WHERE UserId = :id ');
			$stmt->execute(array(':order' => $this->order, ':id' => $this->id));
		}
	}
	
	function getSettings() {
		if($_POST['id'] !== 0) {
			$this->id = $_POST['id'];

			$stmt = $this->db->prepare('SELECT TagColor, NoteOrder FROM user_preferences WHERE UserId = :id ');
			$stmt->execute(array(':id' => $this->id));
			$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$order = $row[0]['NoteOrder'];
			$color = $row[0]['TagColor'];
			$return = array($color, $order);
			echo json_encode($return);
		}
	}
}

?>