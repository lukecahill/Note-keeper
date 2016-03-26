<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	require_once 'db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT NoteTitle, NoteText, NoteId, NoteTags 
							FROM note 
							WHERE NoteComplete = 0"
						);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$result = '';
		
	foreach($rows as $item) {
		
		$tags = '';
		$tags = unserialize($item['NoteTags']);
		
		echo '<div class="note" data-id="' . $item['NoteId'] . '"> 
				<span class="note-id" id="' . $item['NoteId'] . '">Note ID: ' . $item['NoteId']. '</span>
				<h4 class="note-title">' . $item['NoteTitle'] . '</h4>
				<p class="note-text">' . $item['NoteText'] . '</p>';
		
		if(sizeof($tags) > 0 && $tags !== '') {
			foreach($tags as $tag) {
				echo '<span class="note-tags" title="Click to show all notes with this tag." data-tag="' . $tag . '">' . $tag . '</span>';
			}
		}
		
		echo '<div class="note-glyphicons">
			<span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>
			<span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span>
			<span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span>
		</div>';
		
		echo '</div>';
	}
	
	// potentially change this to instead return JSON which will then be parsed by the client-side script.js
} else {
	echo 'No direct access';
}

?>