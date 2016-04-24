<?php

require_once 'note-base.php';

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
			return;
		}
		
		$this->userId = $_POST['userId'];
		$this->complete = $_POST['complete'];
		
		$stmt = $this->db->prepare("SELECT n.NoteTitle, n.NoteText, n.NoteId, n.NoteTags, u.TagColor 
								FROM note n 
								INNER JOIN note_users u ON u.UserId = n.UserId
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
		
		$stmt = $this->db->prepare("SELECT n.NoteTitle, n.NoteText, n.NoteId, n.NoteTags, u.TagColor 
								FROM note n 
								INNER JOIN note_users u ON u.UserId = n.UserId
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
		
		if($count < 0) {
			echo json_encode('none');
			return;
		}
		
		foreach($rows as $item) {
			
			$note = '';
			$tags = unserialize($item['NoteTags']);
			
			$note .= '<div class="note" data-id="' . $item['NoteId'] . '"> 
					<span class="note-id" id="' . $item['NoteId'] . '">Note ID: ' . $item['NoteId']. '</span>
					<h4 class="note-title">' . $item['NoteTitle'] . '</h4>
					<p class="note-text">' . $item['NoteText'] . '</p>';
			
			if(sizeof($tags) > 0 && $tags !== '') {
				foreach($tags as $tag) {
					$color = $item['TagColor'];
					$note .= '<span class="note-tags note-tags note-tags-' . $color . '" title="Click to show all notes with this tag." data-tag="' . $tag . '">' . $tag . '</span>';
				}
			}
			
			if(sizeof($tags) > 0 && $tags !== '') {
				foreach($tags as $tag) {
					
					if(!in_array($tag, $tagList)) { 
						$tagList[] = $tag;
						
						// limit the pre-loaded tags to 5.
						if(5 >= count($tagList)) {
							$checkbox[] = $tag;
						}
					}
				}
			}
			
			if($this->complete == 0) {
				$note .= '<div class="note-glyphicons">
					<span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>
					<span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span>
					<span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span>
					</div>';
			} else {
				$note .= '<div class="note-glyphicons">
					<span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>
					<span class="glyphicon glyphicon-asterisk mark-note-active" title="Mark as active"></span>
					</div>';
			}
			
			$note .= '</div>';
			$notes[] = $note;
		}
		if($this->action !== 'searchnote') $style = '<style> .note .note-tags { background-color: #' . $rows[0]['TagColor'] . '; border-color: #' . $rows[0]['TagColor'] . ' } </style>';
		
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