<?php

	include 'includes/db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT * FROM notes");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($rows as $item) {
		echo $item['NoteId'];
		echo $item['NoteMessage'];
		echo $item['NoteTags'];
	}

?>