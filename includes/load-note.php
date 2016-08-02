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
		if(!isset($_POST['userId']) || !isset($_POST['auth'])) {
			echo json_encode('invalid_request');
			return;
		}
		
		$this->userId = $_POST['userId'];
		$this->complete = $_POST['complete'];
		$auth = $_POST['auth'];

		$stmt = $this->db->prepare("SELECT p.NoteOrder
									FROM user_preferences p
									INNER JOIN note_users u ON u.UserId = p.UserId 
									WHERE u.UserId = :id
									AND u.JsonAuthentication = :auth"
								);
		$stmt->execute(array(':id' => $this->userId, ':auth' => $auth));
		$order = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$order = $order[0]['NoteOrder'];
		
		$stmt = $this->noteOrder($order);
		$stmt = $this->db->prepare($stmt);
		$stmt->execute(array(':userId' => $this->userId, ':complete' => $this->complete));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $stmt->rowCount();
		
		$this->returnNote($rows, $count, $order);
	}
	
	function searchNote() {
		if(!isset($_POST['userId']) || !isset($_POST['auth']) || !isset($_POST['search'])) {
			echo json_encode('invalid_request');
			return;
		}
		
		$this->userId = $_POST['userId'];
		$this->search = $_POST['search'];
		$auth = $_POST['auth'];
		$this->search = '%' . $this->search . '%';
		
		$stmt = $this->db->prepare("SELECT p.NoteOrder, p.SearchOptions 
									FROM user_preferences p
									INNER JOIN note_users u ON u.UserId = p.UserId 
									WHERE p.UserId = :id 
									AND u.JsonAuthentication = :auth
									LIMIT 1");
		$stmt->execute(array(':id' => $this->userId, ':auth' => $auth));
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$order = $row[0]['NoteOrder'];
		
		$searchOptions = $this->getSearchOptions($row);
		$title = $searchOptions[0];
		$text = $searchOptions[1];
		$complete = $searchOptions[2];

		if($complete === 'true') {
			$complete = 1;
		} else {
			$complete = 0;
		}

		$stmt = $this->searchNoteBuild($order, $title, $text);
		$stmt = $this->db->prepare($stmt);
		$stmt->execute(array(':complete' => $complete, ':userId' => $this->userId, ':searchtitle' => $this->search));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $stmt->rowCount();

		$this->returnNote($rows, $count, 'oldest', 'search');
	}
	
	function getSearchOptions($row) {
		$search = unserialize($row[0]['SearchOptions']);
		$searchOptions = array();
		if(sizeof($search) > 0 && $search !== '') {
			foreach($search as $item) {
				$searchOptions[] = $item;
			}
		}

		return $searchOptions;
	}
	
	function returnNote($rows, $count, $order = 'oldest', $type = 'load') {
		$tagList = $checkbox = $merged = $notes = array();
		$color = '';

		if(0 === $count) {
			if($type === 'search') {
				echo json_encode('no_results');
				return;
			}
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
		
		if($order == 'newest') {
			rsort($notes);
		}

		if($this->action !== 'searchnote') $style = $color;
		sort($tagList);

		array_push($merged, $checkbox);
		array_push($merged, $tagList);
		array_push($merged, $notes);
		if($this->action !== 'searchnote') array_push($merged, $style);
		echo json_encode($merged);
	}
	
	function noteOrder($order) {
		$stmt = 'SELECT n.NoteTitle, n.NoteText, n.NoteId, n.NoteTags, 
				p.TagColor, p.NoteOrder
				FROM note n 
				INNER JOIN note_users u ON u.UserId = n.UserId
				INNER JOIN user_preferences p ON p.UserId = u.UserId
				WHERE NoteComplete = :complete
				AND n.UserId = :userId';

		if($order == 'alphabetically') {
			$stmt .= ' ORDER BY NoteTitle';
		} else if($order == 'alpha_backwards') {
			$stmt .= ' ORDER BY NoteTitle DESC';
		} else if($order == 'last_edited') {
			$stmt .= ' ORDER BY NoteLastEdited DESC';
		} else if($order == 'oldest_edited') {
			$stmt .= ' ORDER BY NoteLastEdited ASC';
		}
		
		return $stmt;
	}
	
	function searchNoteBuild($order, $title, $text) {
		$stmt = "SELECT n.NoteTitle, n.NoteText, n.NoteId, 
			n.NoteTags, p.TagColor, p.NoteOrder
			FROM note n 
			INNER JOIN note_users u ON u.UserId = n.UserId
			INNER JOIN user_preferences p ON p.UserId = u.UserId
			WHERE NoteComplete = :complete
			AND n.UserId = :userId";

		if($title === 'true' && $text === 'true') {
			$stmt = "SELECT n.NoteTitle, n.NoteText, n.NoteId, 
			n.NoteTags, p.TagColor, p.NoteOrder
			FROM note n 
			INNER JOIN note_users u ON u.UserId = n.UserId
			INNER JOIN user_preferences p ON p.UserId = u.UserId
			WHERE NoteComplete = :complete
			AND n.UserId = :userId
			AND n.NoteTitle LIKE :searchtitle
			OR n.NoteText LIKE :searchtitle
			AND n.UserId = :userId
			AND NoteComplete = :complete";
		
			$stmt = $this->searchNoteOrder($stmt, $order);
			return $stmt;
		}

		if($title === 'true') {
			$stmt .= " AND n.NoteTitle LIKE :searchtitle";
		}

		if($text === 'true' && $title === 'false') {
			$stmt .= " AND n.NoteText LIKE :searchtitle";
		}

		$stmt = $this->searchNoteOrder($stmt, $order);
		return $stmt;
	}
	
	function searchNoteOrder($stmt, $order) {
		if($order == 'alphabetically') {
			$stmt .= ' ORDER BY NoteTitle';
		} else if($order == 'alpha_backwards') {
			$stmt .= ' ORDER BY NoteTitle DESC';
		} else if($order == 'last_edited') {
			$stmt .= ' ORDER BY NoteLastEdited DESC';
		} else if($order == 'oldest_edited') {
			$stmt .= ' ORDER BY NoteLastEdited ASC';
		}

		return $stmt;
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