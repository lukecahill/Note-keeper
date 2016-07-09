<?php

if(isset($_GET['hash']) && !empty($_GET['hash'])) {
	
	$hash = $_GET['hash'];

	echo $hash;
	
	require_once '../db-connect.inc.php';
	
	$db = Database::ConnectDb();
	$id = $_GET['user'];
	$stmt = $db->prepare('UPDATE note_users SET Active = 1 WHERE UserId = :id');
	$stmt->execute(array(':id' => $id));
	
	echo $id;

} else {
	echo 'No email found are you sure you are meant to be here?';
}

?>