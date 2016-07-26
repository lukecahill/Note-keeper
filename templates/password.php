<?php
$error = '';
if(isset($_POST['new-password']) && isset($_POST['confirm-password']) && isset($_POST['old-password']) && isset($_SESSION['userId'])) {
	
	$old = $_POST['old-password'];
	$new = $_POST['new-password'];
	$confirm = $_POST['confirm-password'];
	$userId = $_SESSION['userId'];
	
	require_once 'includes/db-connect.inc.php';
	$db = Database::ConnectDb();
	
	$stmt = $db->prepare('SELECT UserPassword FROM note_users WHERE UserId = :id');
	$stmt->execute(array(':id' => $userId));
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$encrypted = $results[0]['UserPassword'];
	
	if(password_verify($old, $encrypted)) {
		$new = password_hash($new, PASSWORD_DEFAULT);
		$stmt = $db->prepare('UPDATE note_users SET UserPassword = :password WHERE UserId = :id');
		$stmt->execute(array(':password' => $new,':id' => $userId));
		echo 'Pasword Changed!';
	} else {
		$error = '<span class="validation-error">The password you entered was invalid!</span>';
	}
	
} else if(isset($_POST['change-password-button'])) {
	echo 'No direct access';
}

?>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="change-password-form" class="options-border">
	<h4 id="password-header">
		Change your password here.
	</h4>
	<div id="password-change">
		<div class="form-group">
			<label for="old-password">
				Enter old password:
			</label>
			<input type="password" id="old-password" name="old-password" class="form-control">
			<?php if($error !== '') echo $error; ?>
		</div>

		<div class="form-group">
			<label for="new-password">
				Enter new password:
			</label>
			<input type="password" id="new-password" name="new-password" class="form-control">
		</div>

		<div class="form-group">
			<label for="confirm-password">
				Confirm new password:
			</label>
			<input type="password" id="confirm-password" name="confirm-password" class="form-control">
		</div>

		<button class="btn btn-success" id="change-password-button" name="change-password-button" type="submit">
			<span class="glyphicon glyphicon-asterisk"></span>
			Change Password
		</button>
	</div>
</form>