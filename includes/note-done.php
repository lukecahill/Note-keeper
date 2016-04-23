<?php

require_once 'note-base.php';

class NoteDone extends Note {

	function __construct($id) {
		parent::__construct();
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


if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['noteId']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {	
	$noteId = $_POST['noteId'];
	$complete = $_POST['complete'];
	$note = new NoteDone($noteId);
	
	if($complete == 1) {
		$note->MarkDone();
	} else {
		$note->MarkActive();
	}

	$note = null;
	
} else {
	echo 'No direct access';
}

?>