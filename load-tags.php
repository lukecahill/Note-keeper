<?php

	require_once 'includes/db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT NoteTags FROM note");
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$result = '';
	$tagList = array();
		
	foreach($rows as $item) {
		
		$tags = unserialize($item['NoteTags']);
		foreach($tags as $tag) {
			
			if(!in_array($tag, $tagList)) { 
				$tagList[] = 
				'<div class="checkbox">
				  <label>
					<input type="checkbox" name="new-tag" class="note-tags" data-tag="' . $tag . '" value="' . $tag . '">
					' . $tag . '
				  </label>
				</div>';
			}
		}
	}
	
	//echo '<select id="add-note-tag-selector" class="form-control">';
	foreach($tagList as $tag) {
		echo $tag;
	}
	//echo '</select>';

?>