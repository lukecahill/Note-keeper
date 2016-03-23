<?php

	require_once 'includes/db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT NoteText, NoteId, NoteTags FROM note");
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$result = '';
	// $tagList = array();
		
	foreach($rows as $item) {
		
		$tags = unserialize($item['NoteTags']);
		
		echo '<div class="note">';
		echo '<span class="note-id" id="' . $item['NoteId'] . '">Note ID: ' . $item['NoteId']. '</span>';
		echo '<p class="note-text">' . $item['NoteText'] . '</p>';
		
		foreach($tags as $tag) {
			echo '<span class="note-tags" data-tag="' . $tag . '">' . $tag . '</span>';
			// if(!in_array($tag, $tagList)) { $tagList[] = $tag; }
		}
		
		echo '</div>';
		// echo json_encode(serialize($tagList));
	}

?>