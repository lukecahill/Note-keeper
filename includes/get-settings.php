<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	require_once 'db-connect.inc.php';

	$db = ConnectDb();
	$stmt = $db->prepare("SELECT TagColor 
							FROM note_users
							WHERE UserId = :id"
						);
	$stmt->execute(array(':id' => :id));
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	foreach($rows as $item) {
		
	}

} else {
	echo 'No direct access';
}

?>