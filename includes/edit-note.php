<?php

if(isset($_POST['noteText']) && isset($_POST['noteId'])) {
	
	$note = $_POST['noteText'];
	$noteId = $_POST['noteId'];
	
	require_once 'db-connect.inc.php';
	$db = ConnectDb();
	
	$stmt = $db->prepare('UPDATE note SET NoteText = :text WHERE NoteId = :id ');
	$stmt->execute(array( ':text' => $note, ':id' => $noteId));
	
	echo $noteId . ' has been updated.';
}

?>