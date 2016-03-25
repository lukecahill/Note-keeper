<?php

if(isset($_POST['noteText'])) {
	
	require_once 'db-connect.inc.php';
	$db = ConnectDb();

	$noteText = $_POST['noteText'];
	$noteTags = isset($_POST['noteTags']) ? $_POST['noteTags'] : '';
	$noteTitle = $_POST['noteTitle'];
	
	$noteTags = serialize($noteTags);

	$stmt = $db->prepare('INSERT INTO note(NoteTitle, NoteText, NoteTags) VALUES(:title, :text, :tags)');
	$stmt->execute(array(':title' => $noteTitle,':text' => $noteText, ':tags' => $noteTags));
	
	echo $db->lastInsertId();

} else {
	die('No direct access!');
}

?>