<?php

	include 'includes/db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT NoteText, NoteId, NoteTags FROM note");
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$result = '';
		
	foreach($rows as $item) {
		
		$tags = unserialize($item['NoteTags']);
		
		echo '<div class="note">';
		echo '<span class="note-id" id="' . $item['NoteId'] . '">Note ID: ' . $item['NoteId']. '</span>';
		echo '<span class="note-text">' . $item['NoteText'] . '</span><br>';
		
		foreach($tags as $tag) {
			echo '<span class="note-tags">' . $tag . '</span>';
		}
		
		echo '</div>';
	}

?>