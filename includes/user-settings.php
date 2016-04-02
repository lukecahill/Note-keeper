<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	$user = new UserSettings($_POST['action']);
	
	if($user->method === 'get-tag-color') {
		$user->getTagColor();
	} else if($user->method === 'set-tag-color') {
		$user->setTagColor();
	}

} else {
	echo 'No direct access';
}

class UserSettings {
	public $method = '';
	public $id = 0;
	public $color = '';
	
	function __construct($method) {
		$this->method = $method;
	}
	
	function setTagColor() {
		if($_POST['id'] !== 0) {
			$this->color = $_POST['color'];
			$this->id = $_POST['id'];
			
			require_once 'db-connect.inc.php';
			$db = ConnectDb();
			
			$stmt = $db->prepare('UPDATE note_users SET TagColor = :color WHERE UserId = :id ');
			$stmt->execute(array( ':color' => $this->color, ':id' => $this->id));
		}
	}
	
	function getTagColor() {
		if($_POST['id'] !== 0) {
			$this->id = $_POST['id'];
			
			require_once 'db-connect.inc.php';
			$db = ConnectDb();
			
			$stmt = $db->prepare('SELECT TagColor FROM note_users WHERE UserId = :id ');
			$stmt->execute(array(':id' => $this->id));
			$color = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			echo json_encode($color[0]['TagColor']);
		}
	}
}

?>