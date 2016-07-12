<?php

require_once 'note.php';

class LoadNote extends Note {
	public $userId = 0;
	public $complete = 0;
	public $search = '';
	public $action = '';
	
	function __construct($action) {
		parent::__construct();
		$this->action = $action;
	}
	
	function getNotes() {
		if(!isset($_POST['userId']) || !isset($_POST['complete'])) {
			echo json_encode('invalid');
			return;
		}
		
		$this->userId = $_POST['userId'];
		$this->complete = $_POST['complete'];
		
		$stmt = $this->db->prepare("SELECT n.NoteTitle, n.NoteText, n.NoteId, n.NoteTags, p.TagColor 
								FROM note n 
								INNER JOIN note_users u ON u.UserId = n.UserId
								INNER JOIN user_preferences p ON p.UserId = u.UserId
								WHERE NoteComplete = :complete
								AND n.UserId = :userId"
							);
		
		$stmt->execute(array(':userId' => $this->userId, ':complete' => $this->complete));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $stmt->rowCount();
		
		$this->returnNote($rows, $count);
	}
	
	function searchNote() {
		if(!isset($_POST['userId']) || !isset($_POST['complete']) || !isset($_POST['search'])) {
			return;
		}
		
		$this->userId = $_POST['userId'];
		$this->complete = $_POST['complete'];
		$this->search = $_POST['search'];

		$this->search = '%' . $this->search . '%';
		
		$stmt = $this->db->prepare("SELECT n.NoteTitle, n.NoteText, n.NoteId, n.NoteTags, p.TagColor 
								FROM note n 
								INNER JOIN note_users u ON u.UserId = n.UserId
								INNER JOIN user_preferences p ON p.UserId = u.UserId
								WHERE NoteComplete = :complete
								AND n.UserId = :userId
								AND n.NoteTitle LIKE :searchtitle"
							);
		
		$stmt->execute(array(':complete' => $this->complete, ':userId' => $this->userId, ':searchtitle' => $this->search));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $stmt->rowCount();

		$this->returnNote($rows, $count);
	}
	
	function returnNote($rows, $count) {
		$tagList = $checkbox = $merged = $notes = array();
		$color = '';
		
		if(0 === $count) {
			echo json_encode('none');
			return;
		}
		
		foreach($rows as $item) {
			$tags = unserialize($item['NoteTags']);
			$tagArray = array();

			if(sizeof($tags) > 0 && $tags !== '') {
				foreach($tags as $tag) {
					$tagArray[] = $tag;

					if(!in_array($tag, $tagList)) { 
						$tagList[] = $tag;
						
						// limit the pre-loaded tags to 5.
						if(5 >= count($tagList)) {
							$checkbox[] = $tag;
						}
					}
				}
			}

			$noteArray = array('complete' => $this->complete, 'color' => $item['TagColor'], 'id' => $item['NoteId'], 'title' => $item['NoteTitle'], 'text' => $item['NoteText'], $tagArray);
			$notes[] = $noteArray;
			$color = $item['TagColor'];
		}


		if($this->action !== 'searchnote') $style = $color;

		array_push($merged, $checkbox);
		array_push($merged, $tagList);
		array_push($merged, $notes);
		if($this->action !== 'searchnote') array_push($merged, $style);
		echo json_encode($merged);
	}
}

if(($_SERVER['REQUEST_METHOD'] == 'POST') && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	$note = new LoadNote($_POST['action']);
	
	if($note->action === 'loadnote') {
		$note->getNotes();
	} else if($note->action === 'searchnote') {
		$note->searchNote();
	} else {
		echo json_encode('Action not found');
	}

	$note = null;
	
} else {
	echo json_encode('No direct access');
}

?>