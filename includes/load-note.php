<?php

if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['userId']) && isset($_POST['complete']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

	require_once 'db-connect.inc.php';
	$userId = $_POST['userId'];
	$complete = $_POST['complete'];
	$db = ConnectDb();
	
 	$stmt = $db->prepare("SELECT n.NoteTitle, n.NoteText, n.NoteId, n.NoteTags, u.TagColor 
							FROM note n 
							INNER JOIN note_users u ON u.UserId = n.UserId
							WHERE NoteComplete = :complete
							AND n.UserId = :userId"
						);
	
	$stmt->execute(array(':userId' => $userId, ':complete' => $complete));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$count = $stmt->rowCount();

	$tagList = array();
	$checkbox = array();
	$merged = array();
	$notes = array();
	
	if($count > 0) {
		foreach($rows as $item) {
			
			$note = '';
			$tags = unserialize($item['NoteTags']);
			
			$note .= '<div class="note" data-id="' . $item['NoteId'] . '"> 
					<span class="note-id" id="' . $item['NoteId'] . '">Note ID: ' . $item['NoteId']. '</span>
					<h4 class="note-title">' . $item['NoteTitle'] . '</h4>
					<p class="note-text">' . $item['NoteText'] . '</p>';
			
			if(sizeof($tags) > 0 && $tags !== '') {
				foreach($tags as $tag) {
					$note .= '<span class="note-tags" title="Click to show all notes with this tag." data-tag="' . $tag . '">' . $tag . '</span>';
				}
			}
			
			if(sizeof($tags) > 0 && $tags !== '') {
				foreach($tags as $tag) {
					
					if(!in_array($tag, $tagList)) { 
						$tagList[] = $tag;
						
						// limit the pre-loaded tags to 5.
						if(5 >= count($tagList)) {
							$checkbox[] = $tag;
						}
					}
				}
			}
			
			if($complete == 0) {
				$note .= '<div class="note-glyphicons">
					<span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>
					<span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span>
					<span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span>
					</div>';
			} else {
				$note .= '<div class="note-glyphicons">
					<span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>
					<span class="glyphicon glyphicon-asterisk mark-note-active" title="Mark as active"></span>
					</div>';
			}
			
			$note .= '</div>';
			$notes[] = $note;
		}
		
		$style = '<style> .note .note-tags { background-color: #' . $rows[0]['TagColor'] . '; border-color: #' . $rows[0]['TagColor'] . ' } </style>';
		
		array_push($merged, $checkbox);
		array_push($merged, $tagList);
		array_push($merged, $notes);
		array_push($merged, $style);
		echo json_encode($merged);
		
	} else {
		echo 'none';
	}
	
	// potentially change this to instead return JSON which will then be parsed by the client-side script.js
} else {
	echo 'No direct access';
}

?>