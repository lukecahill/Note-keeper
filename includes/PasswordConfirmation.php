<?php

class PasswordConfirmation {
	public $hash = '';
	public $id = '';
	public $db = null;
	
	function __construct() {
		require_once 'includes/db-connect.inc.php';
		$this->db = Database::ConnectDb();
	}
	
	function check() {
		$stmt = $this->db->prepare('SELECT UserId FROM note_users WHERE UserId = :id AND EmailConfirmation = :hash');
		$stmt->execute(array(':id' => $this->id, ':hash' => $this->hash));
		
		if($stmt->rowCount() != 0) {
			return true;
		} else {
			return false;
		}
	}

    function resetPassword() {
        $new = $_POST['reset-new-password'];
        $confirm = $_POST['reset-confirm-password'];
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
    }
}

if(isset($_GET['hash']) && !empty($_GET['hash']) && isset($_GET['user']) && !empty($_GET['user'])) {
	
	$confirm = new PasswordConfirmation($_GET['hash'], $_GET['user']);
	
	if($confirm->check()) {
        ?>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="change-password-form" class="options-border">
            <?php if($error !== '') echo $error; ?>
            <h4 id="reset-password-header">
                Change your password here.
                <span class="password_span glyphicon glyphicon-chevron-up">
                </span>
            </h4>
            <div id="reset-password-change">

                <div class="form-group">
                    <label for="new-password">
                        Enter new password:
                    </label>
                    <input type="password" id="reset-new-password" name="reset-new-password" class="form-control">
                </div>

                <div class="form-group">
                    <label for="confirm-password">
                        Confirm new password:
                    </label>
                    <input type="password" id="reset-confirm-password" name="reset-confirm-password" class="form-control">
                </div>

                <button class="btn btn-success" id="reset-password-button" name="change-password-button" type="submit">
                    <span class="glyphicon glyphicon-asterisk"></span>
                    Change Password
                </button>
            </div>
        </form>
        <?php
	} 
} else if(isset($_POST['reset-confirm-password']) && isset($_POST['reset-new-password']) && isset($_SESSION['userId'])) {
    $confirm = new PasswordConfirmation();
} else {
    echo json_encode('No email found are you sure you are meant to be here?');
}
?>