<?php

if(isset($_POST['deleteNote']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$delete = $_POST['deleteNote'];
	
	require_once 'db-connect.inc.php';
	$db = ConnectDb();
	
	$stmt = $db->prepare('DELETE FROM note WHERE NoteId = :noteId');
	$stmt->execute(array(':noteId' => $delete));
	
	echo $delete . ' has been deleted.';
} else {
	echo 'No direct access';
}

?>