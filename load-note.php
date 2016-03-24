<?php

	require_once 'includes/db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT NoteText, NoteId, NoteTags FROM note");
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$result = '';
		
	foreach($rows as $item) {
		
		$tags = unserialize($item['NoteTags']);
		
		echo '<div class="note">';
		echo '<span class="note-id" id="' . $item['NoteId'] . '">Note ID: ' . $item['NoteId']. '</span>';
		echo '<p class="note-text">' . $item['NoteText'] . '</p>';
		
		foreach($tags as $tag) {
			echo '<span class="note-tags" title="Click to show all notes with this tag." data-tag="' . $tag . '">' . $tag . '</span>';
		}
		
		echo '</div>';
	}
	
	// potentially change this to instead return JSON which will then be parsed by the client-side script.js

?>