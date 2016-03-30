<?php

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	
	if(isset($_POST['id'])) {
		$color = $_POST['color'];
		$id = $_POST['id'];
		
		require_once 'db-connect.inc.php';
		$db = ConnectDb();
		
		$stmt = $db->prepare('UPDATE note_users SET TagColor = :color WHERE UserId = :id ');
		$stmt->execute(array( ':color' => $color, ':id' => $id));
		
		echo 'Updated!';
	}
	
	// other user settings can then be updated in an if-else

} else {
	echo 'No direct access';
}

?>