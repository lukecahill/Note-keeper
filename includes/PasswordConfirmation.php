<?php

/**
* PasswordConfirmation checks that the URL is valid, and will allow the user to reset their password. 
*
* @package  Note Keeper
* @author   Luke Cahill
* @access   public
*/
class PasswordConfirmation {
	public $hash = '';
	public $id = '';
	public $db = null;
    public $error = '';
    public $success = false;
	
	/**  
	* Constructs the PasswordConfirmation class. 
    * 
	* @param string $hash Hash confirmation that the password should be reset
	* @param string $id User ID
	*
	* @return object 
	*/
	function __construct($hash, $id) {
		require_once 'includes/db-connect.inc.php';
		$this->db = Database::ConnectDb();
        $this->id = $id;
        $this->hash = $hash;
	}
	
	/**  
	* Checks that the ID and hash which have been entered in the URL are valid. 
	*
	* @return bool - true if account and confirmation exists, false if not.
	*/
	function check() {
		$stmt = $this->db->prepare('SELECT UserId FROM note_users WHERE UserId = :id AND EmailConfirmation = :hash AND Active = 2');
		$stmt->execute(array(':id' => $this->id, ':hash' => $this->hash));
		
		if($stmt->rowCount() != 0) {
			return true;
		} else {
			return false;
		}
	}

	/**  
	* Updates the users password in the database.
	*
	* @return void
	*/
    function resetPassword() {
        $newPassword = $_POST['reset-new-password'];
        $confirm = $_POST['reset-confirm-password'];

        if($newPassword !== $confirm) {
            $this->success = false;
            $this->error = 'Ensure that both password entered are the same!';
            return false;
        }

		$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('UPDATE note_users SET UserPassword = :password, Active = 1, EmailConfirmation = "" WHERE UserId = :id');
        $stmt->execute(array(':password' => $passwordHash,':id' => $this->id));

        $this->success = true;
        return true;
    }

	/**  
	* Generates the HTML form used by the user to reset their password.
	*
	* @return void
	*/
    function showPasswordForm() {
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

                <button class="btn btn-success" id="reset-password-button" name="reset-password-button" type="submit">
                    <span class="glyphicon glyphicon-asterisk"></span>
                    Change Password
                </button>
            </div>
            <input type="hidden" name="userId" value="<?php echo $_GET['user'] ?>"/>
        </form>
    <?php
    }
}

if(isset($_GET['hash']) && !empty($_GET['hash']) && isset($_GET['user']) && !empty($_GET['user'])) {

	$confirm = new PasswordConfirmation($_GET['hash'], $_GET['user']);
    $error = $confirm->error;
	if($confirm->check()) {
        $confirm->showPasswordForm();
	} else {
        echo 'Link invalid. Please generate a new one';
    }
} else if(isset($_POST['reset-password-button'])) {
	$reset = new PasswordConfirmation('0', $_POST['userId']);
    $status = $reset->resetPassword();

        if($status) {
        ?>
            <h3>
                Your password has been changed successfully
            </h3>
            <p>
                Password reset successful, please <a href="login.php">log in</a>
            </p>
        <?php
        } else {
            $confirm->showPasswordForm();
        }
    } else {
    echo json_encode('No email found are you sure you are meant to be here?');
}
?>