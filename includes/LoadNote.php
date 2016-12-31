<?php

spl_autoload_register(function ($class_name) {
	include $class_name . '.php';
});

/**
* LoadNote is used to get the users notes from the database. 
* Also allows the user to search the database for the user specified text, 
* and returns the results in the order the users has specified.
* Inherits from the base class Note.
*
* @package  Note Keeper
* @author   Luke Cahill
* @access   public
*/
class LoadNote extends Note {
	const PREGENERATED_TAGS = 5;

	public $userId = 0;
	public $complete = 0;
	public $search = '';
	public $action = '';
	
	/**  
	* Construct the LoadNote class
	*
	* @param string $action The action of what method to call
	*/
	function __construct($action) {
		parent::__construct();
		$this->action = $action;
	}
	
	/**
	* Gets all of the users notes
	*
	* @return void
	*/
	function getNotes() {
		if(!isset($_POST['userId']) || !isset($_POST['auth'])) {
			echo json_encode('invalid_request');
			return;
		}
		
		$this->userId = $_POST['userId'];
		$this->complete = $_POST['complete'];
		$auth = $_POST['auth'];

		$stmt = $this->db->prepare("SELECT p.NoteOrder
									FROM note_user_preferences p
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
	
	/**
	* Searches the users notes by what is entered in the search box.
	*
	* @return void
	*/
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
									FROM note_user_preferences p
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

		$stmt = $this->searchNoteBuild($order, $title, $text, $complete);
		$stmt = $this->db->prepare($stmt);
		$stmt->execute(array(':userId' => $this->userId, ':searchtitle' => $this->search));
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $stmt->rowCount();

		$this->returnNote($rows, $count, 'oldest', 'search');
	}
	
	/**
	* Searches the users notes by what is entered in the search box.
	*
	* @param array $row Returns the users selected search options
	* 
	* @return array
	*/
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
	
	/**
	* Returns the JSON representation of the notes.
	*
	* @param array $rows The rows that were returned from the load/search
	* @param int $count The count of the number of rows found
	* @param string $order The order the notes should be returned in 
	* @param string $type If this was a search or a normal load.
	*
	* @return void
	*/
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
						
						// limit the pre-loaded tags to value of PREGENERATED_TAGS.
						if(self::PREGENERATED_TAGS >= count($tagList)) {
							$checkbox[] = $tag;
						}
					}
				}
			}

			$noteArray = array('complete' => $item['NoteComplete'], 'color' => $item['TagColor'], 'id' => $item['NoteId'], 'title' => $item['NoteTitle'], 'text' => $item['NoteText'], $tagArray);
			$notes[] = $noteArray;
			$color = $item['TagColor'];
		}
		
		if($order == 'newest') {
			rsort($notes);
		}

		if($this->action !== 'searchnote') $style = $color;	// is this still needed?
		sort($tagList);

		array_push($merged, $checkbox);
		array_push($merged, $tagList);
		array_push($merged, $notes);
		if($this->action !== 'searchnote') array_push($merged, $style);	// same question as above.
		echo json_encode($merged);
	}
	
	/**
	* Returns the JSON representation of the notes.
	*
	* @param string $order
	*
	* @return string SQL statement
	*/
	function noteOrder($order) {
		$stmt = 'SELECT n.NoteTitle, n.NoteText, n.NoteId, n.NoteTags, n.NoteComplete,
				p.TagColor, p.NoteOrder
				FROM notes n 
				INNER JOIN note_users u ON u.UserId = n.UserId
				INNER JOIN note_user_preferences p ON p.UserId = u.UserId
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
	
	/**
	* Returns the JSON representation of the notes.
	*
	* @param string $order The order the users options indicated the notes should be displayed
	* @param string $title The option of if the notes should be searched by the title
	* @param string $text The option of if the notes should be searched by the title
	* @param bool $showComplete The option of if the notes should be searched if they are completed.
	*
	* @return string SQL statement
	*/
	function searchNoteBuild($order, $title, $text, $showComplete) {
		$stmt = "SELECT n.NoteTitle, n.NoteText, n.NoteId, 
			n.NoteTags, n.NoteComplete, p.TagColor, p.NoteOrder
			FROM notes n 
			INNER JOIN note_users u ON u.UserId = n.UserId
			INNER JOIN note_user_preferences p ON p.UserId = u.UserId
			WHERE n.UserId = :userId";

		if($title === 'true' && $text === 'true') {
			$stmt = "SELECT n.NoteTitle, n.NoteText, n.NoteId, 
			n.NoteTags, n.NoteComplete, p.TagColor, p.NoteOrder
			FROM notes n 
			INNER JOIN note_users u ON u.UserId = n.UserId
			INNER JOIN note_user_preferences p ON p.UserId = u.UserId
			WHERE n.UserId = :userId
			AND n.NoteTitle LIKE :searchtitle";
			
			if($showComplete === 0) {
				$stmt .= " AND n.NoteComplete = 0";
			}

			$stmt .= " OR n.UserId = :userId
			AND n.NoteText LIKE :searchtitle";
			
			if($showComplete === 0) {
				$stmt .= " AND n.NoteComplete = 0";
			}
			
			$stmt = $this->searchNoteOrder($stmt, $order);
			return $stmt;
		}

		if($title === 'true') {
			$stmt .= " AND n.NoteTitle LIKE :searchtitle";
		}

		if($text === 'true' && $title === 'false') {
			$stmt .= " AND n.NoteText LIKE :searchtitle";
		}
		
		if($showComplete === 0) {
			$stmt .= " AND n.NoteComplete = 0";
		}

		$stmt = $this->searchNoteOrder($stmt, $order);
		return $stmt;
	}
	
	/**
	* Returns the JSON representation of the notes.
	*
	* @param string $stmt SQL statement which will be built
	* @param string $order The order the notes should be returned.
	*
	* @return string SQL statement
	*/
	function searchNoteOrder($stmt, $order) {
		if($order == 'alphabetically') {
			$stmt .= ' ORDER BY n.NoteTitle';
		} else if($order == 'alpha_backwards') {
			$stmt .= ' ORDER BY n.NoteTitle DESC';
		} else if($order == 'last_edited') {
			$stmt .= ' ORDER BY n.NoteLastEdited DESC';
		} else if($order == 'oldest_edited') {
			$stmt .= ' ORDER BY n.NoteLastEdited ASC';
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