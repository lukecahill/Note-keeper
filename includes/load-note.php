<?php

if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['userId']) && isset($_POST['complete']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

	require_once 'db-connect.inc.php';
	$userId = $_POST['userId'];
	$complete = $_POST['complete'];
	$db = ConnectDb();
	
 	$stmt = $db->prepare("SELECT NoteTitle, NoteText, NoteId, NoteTags 
							FROM note 
							WHERE NoteComplete = :complete
							AND UserId = :userId"
						);
	
	$stmt->execute(array(':userId' => $userId, ':complete' => $complete));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$count = $stmt->rowCount();
	
	if($count > 0) {
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
			
			if($complete == 0) {
				echo '<div class="note-glyphicons">
					<span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>
					<span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span>
					<span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span>
					</div>';
			} else {
				echo '<div class="note-glyphicons">
					<span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>
					<span class="glyphicon glyphicon-asterisk mark-note-active" title="Mark as active"></span>
					</div>';
			}
			
			echo '</div>';
		}
	} else {
		echo 'none';
	}
	
	// potentially change this to instead return JSON which will then be parsed by the client-side script.js
} else {
	echo 'No direct access';
}

?>