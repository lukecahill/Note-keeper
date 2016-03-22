<?php

	include 'includes/db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT NoteText FROM note");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($rows as $item) {
		echo $item['NoteText'];
	}

?>