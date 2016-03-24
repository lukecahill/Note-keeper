<?php

if(isset($_POST['noteTags'])) {
	
	require_once 'includes/db-connect.inc.php';
	$db = ConnectDb();

	$noteTags = $_POST['noteTags'];
	
	// TODO: actually add this to the database!
	
	// $noteTags = serialize($noteTags); // don't think this needs to be serialised now - since just a string will be sent across.

	$stmt = $db->prepare('INSERT INTO tags(NoteTags) VALUES(:tags)');
	$stmt->execute(array(':tags' => $noteTags));

} else {
	die('No direct access!');
}

?>