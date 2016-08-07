<?php	

require_once 'note.php';

class NoteApi extends Note {

	
	function __construct() {
		parent::__construct();
	}

	function addNote($id, $text, $tags, $title) {
		$tags = serialize($tags);
		$text = nl2br($text);
		$stmt = $this->db->prepare('INSERT INTO note(NoteTitle, NoteText, NoteTags, UserId) VALUES (:title, :text, :tags, :userId)');
		$status = $stmt->execute(array(':title' => $title, ':text' => $text, ':tags' => $tags, ':userId' => $id));
		
		$return = array();
		
		array_push($return, $status);
		array_push($return, $this->db->lastInsertId());
		
		echo json_encode($return);
	}

	function deleteNote($noteId) {
		$stmt = $this->db->prepare('DELETE FROM note WHERE NoteId = :noteId');
		$status = $stmt->execute(array(':noteId' => $noteId));
		
		echo $status;
	}

	function editNote($id, $text, $title, $tags) {
		
		$tags = serialize($tags);
		$text = nl2br($text);
		$stmt = $this->db->prepare('UPDATE note SET NoteText = :text, NoteTitle = :title, NoteTags = :tags, NoteLastEdited = CURRENT_TIMESTAMP() WHERE NoteId = :id ');
		$status = $stmt->execute(array(':text' => $text, ':title' => $title, ':tags' => $tags, ':id' => $id));
		
		echo $status;
	}
	
	function MarkDone($id) {
		$stmt = $this->db->prepare('UPDATE note SET NoteComplete = 1, NoteLastEdited = CURRENT_TIMESTAMP() WHERE NoteId = :id');
		$status = $stmt->execute(array(':id' => $id));
		echo $status;
	}

	function MarkActive($id) {
		$stmt = $this->db->prepare('UPDATE note SET NoteComplete = 0, NoteLastEdited = CURRENT_TIMESTAMP() WHERE NoteId = :id');
		$stmt->execute(array(':id' => $id));
		$status = $stmt->execute(array(':id' => $id));
		echo $status;
	}
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	$action = $_POST['action'];
	$note = new NoteApi();

	if(isset($_POST['noteText']) && ($action === 'addnote')) {
		$tags = isset($_POST['noteTags']) ? $_POST['noteTags'] : '';
		$note->addNote($_POST['userId'], $_POST['noteText'], $tags, $_POST['noteTitle']);
	} else if(isset($_POST['noteId']) && ($action === 'deletenote')) {
		$note->deleteNote($_POST['noteId']);
	} else if(($action === 'editnote') && isset($_POST['noteText']) && isset($_POST['noteId'])) {
		$tags = isset($_POST['noteTags']) ? $_POST['noteTags'] : '';
		$note->editNote($_POST['noteId'], $_POST['noteText'], $_POST['noteTitle'], $tags);
	} else if((isset($_POST['complete'])) && ($action === 'setcomplete')) {
		$complete = $_POST['complete'];
		if($complete == 1) {
			$note->MarkDone($_POST['noteId']);
		} else {
			$note->MarkActive($_POST['noteId']);
		}
	}

	$note = null;

} else {
	die('No direct access!');
}

?>