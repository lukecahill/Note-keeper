<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	$action = $_POST['action'];
	$note = new NoteEdit();

	if(isset($_POST['noteText']) && ($action === 'addnote')) {
		$tags = isset($_POST['noteTags']) ? $_POST['noteTags'] : '';
		$note->addNote($_POST['userId'], $_POST['noteText'], $tags, $_POST['noteTitle']);
	} else if(isset($_POST['deleteNote']) && ($action === 'deletenote')) {
		$note->deleteNote($_POST['deleteNote']);
	} else if(($action === 'editnote') && isset($_POST['noteText']) && isset($_POST['noteId'])) {
		$tags = isset($_POST['noteTags']) ? $_POST['noteTags'] : '';
		$note->editNote($_POST['noteId'], $_POST['noteText'], $_POST['noteTitle'], $tags);
	}

	$note = null;

} else {
	die('No direct access!');
}

class NoteEdit {
	public $db = null;

	function __construct() {
		require_once 'db-connect.inc.php';
		$this->db = Database::ConnectDb();
	}

	function addNote($id, $text, $tags, $title) {
		$tags = serialize($tags);

		$stmt = $this->db->prepare('INSERT INTO note(NoteTitle, NoteText, NoteTags, UserId) VALUES (:title, :text, :tags, :userId)');
		$stmt->execute(array(':title' => $title, ':text' => $text, ':tags' => $tags, ':userId' => $id));
		
		echo $this->db->lastInsertId();
	}

	function deleteNote($noteId) {
		$stmt = $this->db->prepare('DELETE FROM note WHERE NoteId = :noteId');
		$stmt->execute(array(':noteId' => $noteId));
		echo 'Complete';
	}

	function editNote($id, $text, $title, $tags) {
		
		$tags = serialize($tags);
		$stmt = $this->db->prepare('UPDATE note SET NoteText = :text, NoteTitle = :title, NoteTags = :tags WHERE NoteId = :id ');
		$stmt->execute(array(':text' => $text, ':title' => $title, ':tags' => $tags, ':id' => $id));
		
		echo $id . ' has been updated.';
	}
}

?>