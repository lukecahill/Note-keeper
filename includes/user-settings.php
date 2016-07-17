<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	$user = new UserSettings($_POST['action'], $_POST['id']);
	
	if($user->method === 'get-settings') {
		$user->getSettings();
	} else if($user->method === 'set-tag-color') {
		$user->setTagColor();
	} else if($user->method === 'set-note-order') {
		$user->setNoteOrder();
	} else if($user->method === 'set-search-parameters') {
		$user->setSearchParameters();
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
	
	function __construct($method, $id) {
		$this->method = $method;
		require_once 'db-connect.inc.php';
		$this->db = Database::ConnectDb();
		
		if($id !== 0) { 
			$this->id = $id; 
		} else {
			die(json_encode('invalid request'));
		}
		
	}
	
	function setTagColor() {
		$this->color = $_POST['color'];
		
		$stmt = $this->db->prepare('UPDATE user_preferences SET TagColor = :color WHERE UserId = :id ');
		$stmt->execute(array( ':color' => $this->color, ':id' => $this->id));
	}
	
	function setNoteOrder() {
		$this->order = $_POST['order'];
		
		$stmt = $this->db->prepare('UPDATE user_preferences SET NoteOrder = :order WHERE UserId = :id ');
		$stmt->execute(array(':order' => $this->order, ':id' => $this->id));
	}
	
	function setSearchParameters() {
		$title = $_POST['title'];
		$text = $_POST['text'];
		$arr = array($title, $text);
		$arr = serialize($arr);
		$stmt = $this->db->prepare('UPDATE user_preferences SET SearchOptions = :options WHERE UserId = :id');
		$stmt->execute(array(':options' => $arr, ':id' => $this->id));
	}
	
	function getSettings() {
		$stmt = $this->db->prepare('SELECT TagColor, NoteOrder, SearchOptions FROM user_preferences WHERE UserId = :id ');
		$stmt->execute(array(':id' => $this->id));
		$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$search = unserialize($row[0]['SearchOptions']);
		$searchOptions = array();
		if(sizeof($search) > 0 && $search !== '') {
			foreach($search as $item) {
				$searchOptions[] = $item;
			}
		}
		
		$order = $row[0]['NoteOrder'];
		$color = $row[0]['TagColor'];
		$title = $searchOptions[0];
		$text = $searchOptions[1];
		$return = array($color, $order, $title, $text);
		echo json_encode($return);
	}
}

?>