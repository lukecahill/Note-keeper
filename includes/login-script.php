<?php

spl_autoload_register(function ($class_name) {
	include $class_name . '.php';
});

/**
* Login is used to log the user into the system. Generates a JSON token for authentication. 
* Checks the login details are correct. Also logs the users IP address in the system.
* Inherits from the base class Authentication.
*
* @package  Note Keeper
* @author   Luke Cahill
* @access   public
*/
class Login extends Authentication {
	const MAXIMUM_PAST_IPS = 5;
	public $error = '';

	/**  
	* Construct the Login class
	*
	* @param string $email The users email which was used to log in
	* @param string $password The password the user entered when logging in
	*/
	function __construct($email, $password) {
		parent::__construct($email, $password);
	}

	/**  
	* Check the email and password entered were not blank
	*
	* @return bool Depending on if the passwords entered were not empty
	*/
	function checkValid() {
		if(!isset($this->email) || trim($this->email) == '' || !isset($this->password) || trim($this->password) == '') {
			$this->error = "<span class='validation-error'>Not all fields were entered</span>";
			return false;
		} else {
			return true;
		}
	}
	
	/**  
	* Generates a JSON token from the current datetime, and a random number. 
	* 	These are then appended and hashed using SHA1
	*
	* @return string $authentication With the string SHA1 token hash
	*/
	function generateJsonAuthentication() {
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		
		$random = rand();
		$shaRandom = sha1($random);
		$authentication = sha1($shaRandom . $timestamp);
		
		return $authentication;
	}
	
	/**  
	* Log the users IP address in the database
	*
	* @param string $past Serialised array of the most recent up to the value of MAXIMUM_PAST_IPS IP addresses
	*
	* @return string $past Serialised array of the most recent up to the value of MAXIMUM_PAST_IPS IP addresses
	*/
	function logIpAddress($past) {
		$count = 0;
		if(!empty($past) || $past != '') {
			$past = unserialize($past);
			$count = count($past);
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		if($ip !== '::1') {
			if(strpos($ip, ':') !== false) {
				$ip = array_pop(explode(':', $ip));
			}
		}
		
		if($count > 0 && $count === self::MAXIMUM_PAST_IPS) {
			array_shift($past);
		}
		
		$past[] = $ip;
		$past = serialize($past);
		return $past;
	}
	
	/**  
	* Update the users most recent IP, and their JSON token in the database
	*
	* @param string $past Serialised array of the most recent up to 5 IP addresses
	* @param string $authentication JSON token for DB authentication
	* @param string $userId The MD5 hashed value of the users ID
	*
	* @return bool $status If the SQL statement was successful or not
	*/
	function updateUser($past, $authentication, $userId, $theme) {
		$stmt = $this->db->prepare("UPDATE note_users SET JsonAuthentication = :json, RecentIps = :ips, PasswordAttempts = 0 WHERE UserId = :id");
		$stmt->execute(array(':json' => $authentication, ':ips' => $past, ':id' => $userId));

		session_start();
		$_SESSION['user'] = $this->email;
		$_SESSION['userId'] = $userId;
		$_SESSION['authentication'] = $authentication;
		$_SESSION['theme'] = $theme;
		die(header('Location: index.php'));
	}
	
	/**  
	* Verify the users password hash.
	*
	* @param string $encrypted The encrypted version of the users password
	* @param string $userId The MD5 hashed value of the users ID
	* @param string $authentication JSON token for DB authentication
	*
	* @return void
	*/
	function verifyPassword($encrypted, $userId, $authentication){
		if(password_verify($this->password, $encrypted)) {
			return true;
		} else {
			$sql = $this->db->prepare("SELECT PasswordAttempts FROM note_users WHERE UserId = :id LIMIT 1");
			$sql->execute(array(':id' => $userId));
			$result = $sql->fetchAll(PDO::FETCH_ASSOC);
			if($result[0]['PasswordAttempts'] === '4') {
				$stmt = $this->db->prepare("UPDATE note_users SET Active = 2 WHERE UserId = :id");
				$stmt->execute(array(':id' => $userId));
				$this->error = "<span class='validation-error'>The account has been locked due to too many invalid password attempts.</span>";
				return false;
			} else {
				$stmt = $this->db->prepare("UPDATE note_users SET PasswordAttempts = PasswordAttempts+1 WHERE UserId = :id");
				$stmt->execute(array(':id' => $userId));
				$this->error = "<span class='validation-error'>Username/Password invalid</span>";
				return false;
			}
		}
	}

	/**  
	* Gets the relevant information from the DB for use with the other functions. 
	* 	This is also what mainly calls the other functions in the class. 
	*
	* @return void
	*/
	function verify() {
		$stmt = $this->db->prepare('SELECT u.UserEmail, u.UserPassword, u.UserId, u.Active, u.RecentIps, p.ColorTheme
								FROM note_users u
								INNER JOIN user_preferences p ON p.UserId = u.UserId
								WHERE u.UserEmail = :email 
								LIMIT 1'
							);
		$stmt->execute(array(':email' => $this->email));
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(count($results) == 0) {
			$this->error = "<span class='validation-error'>Username/Password invalid</span>";
		} else if($results[0]['Active'] == 0) {
			$this->error = "<span class='validation-error'>This email address has not been confimred! Please check your email and follow the confirmation link.</span>";
		} else if($results[0]['Active'] == 2) {
			$this->error = "<span class='validation-error'>The account has been locked due to too many invalid password attempts - <a href='password-reset.php'>reset it here</a>.</span>";
		} else {
			$encrypted = $results[0]['UserPassword'];
			$userId = $results[0]['UserId'];
			$theme = $results[0]['ColorTheme'];
			$pastIps = $this->logIpAddress($results[0]['RecentIps']);
			$authentication = $this->generateJsonAuthentication();
			$status = $this->verifyPassword($encrypted, $userId, $authentication);
			
			if($status == 1) {
				$this->updateUser($pastIps, $authentication, $userId, $theme);
			}
		}
	}
}

if(isset($_POST['username']) && isset($_POST['password']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$email = $_POST['username'];
	$password = $_POST['password'];
	$login = new Login($email, $password);
	
	if($login->checkValid()) {
		$results = $login->verify();
	}
}

?>