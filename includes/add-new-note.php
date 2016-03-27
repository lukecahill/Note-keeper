<?php

if(isset($_POST['noteText']) && $_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	require_once 'db-connect.inc.php';
	$db = ConnectDb();

	$noteText = $_POST['noteText'];
	$noteTags = isset($_POST['noteTags']) ? $_POST['noteTags'] : '';
	$noteTitle = $_POST['noteTitle'];
	$userId = $_POST['userId'];
	
	$noteTags = serialize($noteTags);

	$stmt = $db->prepare('INSERT INTO note(NoteTitle, NoteText, NoteTags, UserId) VALUES (:title, :text, :tags, :userId)');
	$stmt->execute(array(':title' => $noteTitle,':text' => $noteText, ':tags' => $noteTags, ':userId' => $userId));
	
	echo $db->lastInsertId();

} else {
	die('No direct access!');
}

?>