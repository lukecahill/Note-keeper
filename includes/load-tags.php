<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	require_once 'db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT NoteTags 
							FROM note 
							WHERE NoteComplete = 0"
						);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$result = '';
	$tagList = array();
	$checkbox = array();
		
	foreach($rows as $item) {
		
		$tags = unserialize($item['NoteTags']);
		
		if(sizeof($tags) > 0 && $tags !== '') {
			foreach($tags as $tag) {
				
				if(!in_array($tag, $tagList)) { 
					$tagList[] = $tag;
					
					// limit the pre-loaded tags to 5.
					if(5 >= count($tagList)) {
						$checkbox[] = 
							'<div class="checkbox">
							  <label>
								<input type="checkbox" name="new-tag" data-tag="' . $tag . '" value="' . $tag . '">
								' . $tag . '
							  </label>
							</div>';
					}
				}
			}
		}
	}
	
	foreach($checkbox as $tag) {
		echo $tag;
	}
	
	// change this to return JSON which can then be decoded server-side and will also populate an option box to pick which tag to show.

} else {
	echo 'No direct access';
}

?>