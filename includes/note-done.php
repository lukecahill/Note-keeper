<?php

if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['noteId']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	$noteId = $_POST['noteId'];
	$complete = $_POST['complete'];
	require_once 'db-connect.inc.php';
	$db = Database::ConnectDb();
	
	if($complete == 1) {
		$stmt = $db->prepare('UPDATE note SET NoteComplete = 1 WHERE NoteId = :id');
		$stmt->execute(array(':id' => $noteId));
	} else {
		$stmt = $db->prepare('UPDATE note SET NoteComplete = 0 WHERE NoteId = :id');
		$stmt->execute(array(':id' => $noteId));
	}
	
} else {
	echo 'No direct access';
}

?>