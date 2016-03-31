<?php

if(isset($_POST['noteText']) && isset($_POST['noteId']) && $_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	$noteId = $_POST['noteId'];
	$noteText = $_POST['noteText'];
	$noteTags = isset($_POST['noteTags']) ? $_POST['noteTags'] : '';
	$noteTitle = $_POST['noteTitle'];
	
	$noteTags = serialize($noteTags);
	
	require_once 'db-connect.inc.php';
	$db = ConnectDb();
	
	$stmt = $db->prepare('UPDATE note SET NoteText = :text, NoteTitle = :title, NoteTags = :tags WHERE NoteId = :id ');
	$stmt->execute(array(':text' => $noteText, ':title' => $noteTitle, ':tags' => $noteTags, ':id' => $noteId));
	
	echo $noteId . ' has been updated.';
} else {
	echo 'No direct access';
}

?>