<?php

if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['noteId'])) {
	
	$noteId = $_POST['noteId'];
	require_once 'db-connect.inc.php';
	$db = ConnectDb();
	$stmt = $db->prepare('UPDATE note SET NoteComplete = 1 WHERE NoteId = :id');
	$stmt->execute(array(':id' => $noteId));
	
} else {
	echo 'No direct access';
}

?>