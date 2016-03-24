<?php

if(isset($_POST['noteText'])) {
	
	require_once 'includes/db-connect.inc.php';
	$db = ConnectDb();

	$noteText = $_POST['noteText'];
	$noteTags = $_POST['noteTags'];
	$noteTitle = $_POST['noteTitle'];
	
	// TODO: add the title to the database and then the statements below
	
	$noteTags = serialize($noteTags);

	$stmt = $db->prepare('INSERT INTO note(NoteText, NoteTags) VALUES(:text, :tags)');
	$stmt->execute(array(':text' => $noteText, ':tags' => $noteTags));

} else {
	die('No direct access!');
}

?>