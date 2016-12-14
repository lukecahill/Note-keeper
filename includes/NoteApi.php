<?php	

spl_autoload_register(function ($class_name) {
	include $class_name . '.php';
});

/**
* NoteApi is used to add, delete, edit and mark notes as active/completed. 
* Inherits from the base class Note
*
* @package  Note Keeper
* @author   Luke Cahill
* @access   public
*/
class NoteApi extends Note {

	/**  
	* Construct the NoteApi class
	*
	*/
	function __construct() {
		parent::__construct();
	}

	/**  
	* Adds the note to the database
	*
	* @param string $id The MD5 hashed value of the users ID
	* @param string $text The text from the new note
	* @param string $tags The array of the tags the user has added to the note.
	* @param string $title The title of the new note
	*
	* @return void
	*/
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

	/**  
	* Delete the note the user has chosen.
	*
	* @param string $noteId The ID of the note to update
	*
	* @return void
	*/
	function deleteNote($noteId) {
		$stmt = $this->db->prepare('DELETE FROM note WHERE NoteId = :noteId');
		$status = $stmt->execute(array(':noteId' => $noteId));
		
		echo $status;
	}

	/**  
	* Edits the users note in the database
	*
	* @param string $id The MD5 hashed value of the users ID
	* @param string $text The text from the new note
	* @param string $title The title of the new note
	* @param string $tags The array of the tags the user has added to the note.
	*
	* @return void
	*/
	function editNote($id, $text, $title, $tags) {
		
		$tags = serialize($tags);
		$text = nl2br($text);
		$stmt = $this->db->prepare('UPDATE note SET NoteText = :text, NoteTitle = :title, NoteTags = :tags, NoteLastEdited = CURRENT_TIMESTAMP() WHERE NoteId = :id ');
		$status = $stmt->execute(array(':text' => $text, ':title' => $title, ':tags' => $tags, ':id' => $id));
		
		echo $status;
	}
	
	/**  
	* Update the note the user has marked as complete
	*
	* @param string $id The ID of the note marked as done
	*
	* @return void
	*/
	function MarkDone($id) {
		$stmt = $this->db->prepare('UPDATE note SET NoteComplete = 1, NoteLastEdited = CURRENT_TIMESTAMP() WHERE NoteId = :id');
		$status = $stmt->execute(array(':id' => $id));
		echo $status;
	}

	/**  
	* Update the note the user has marked as active
	*
	* @param string $id The ID of the note marked as active
	*
	* @return void
	*/
	function MarkActive($id) {
		$stmt = $this->db->prepare('UPDATE note SET NoteComplete = 0, NoteLastEdited = CURRENT_TIMESTAMP() WHERE NoteId = :id');
		$stmt->execute(array(':id' => $id));
		$status = $stmt->execute(array(':id' => $id));
		echo $status;
	}

	function shareNotes() {
		
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