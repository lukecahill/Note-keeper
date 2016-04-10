<?php

if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['noteId']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	$noteId = $_POST['noteId'];
	$complete = $_POST['complete'];
	$note = new NoteDone($noteId);
	
	if($complete == 1) {
		$note->MarkDone();
	} else {
		$note->MarkActive();
	}
	
} else {
	echo 'No direct access';
}

class NoteDone {
	public $db = null;
	public $id = 0;

	function __construct($id) {
		require_once 'db-connect.inc.php';
		$db = Database::ConnectDb();
		$this->id = $id;
	}

	function MarkDone() {
		$stmt = $this->db->prepare('UPDATE note SET NoteComplete = 1 WHERE NoteId = :id');
		$stmt->execute(array(':id' => $this->id));
	}

	function MarkActive() {
		$stmt = $this->db->prepare('UPDATE note SET NoteComplete = 0 WHERE NoteId = :id');
		$stmt->execute(array(':id' => $this->id));
	}
}

?>