<?php

require_once 'authentication.php';

class Login extends Authentication {
	public $error = '';

	function __construct($email, $password) {
		parent::__construct($email, $password);
	}

	function checkValid() {
		if($this->email == '' || $this->password == '') {
			$this->error = "<span class='validation-error'>Not all fields were entered</span>";
			return false;
		} else {
			return true;
		}
	}
	
	function generateJsonAuthentication($email) {
		$date = new DateTime();
		$timestamp = $date->getTimestamp();
		$authentication = md5($email);
		$authentication .= (string)md5($timestamp);
		
		return $authentication;
	}
	
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
		
		if($count > 0 && $count === 5) {
			array_shift($past);
		}
		
		$past[] = $ip;
		$past = serialize($past);
		return $past;
	}
	
	function updateUser($past, $authentication, $userId) {
		$stmt = $this->db->prepare("UPDATE note_users SET JsonAuthentication = :json, RecentIps = :ips WHERE UserId = :id");
		$status = $stmt->execute(array(':json' => $authentication, ':ips' => $past, ':id' => $userId));
		return $status;
	}
	
	function verifyPassword($encrypted, $userId, $authentication){
		if(password_verify($this->password, $encrypted)) {
			session_start();
			$_SESSION['user'] = $this->email;
			$_SESSION['userId'] = $userId;
			$_SESSION['authentication'] = $authentication;
			die(header('Location: index.php'));
		} else {
			$this->error = "<span class='validation-error'>Username/Password invalid</span>";
		}
	}

	function verify() {
		$stmt = $this->db->prepare('SELECT UserEmail, UserPassword, UserId, Active, RecentIps
								FROM note_users 
								WHERE UserEmail = :email 
								LIMIT 1'
							);
		$stmt->execute(array(':email' => $this->email));
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(count($results) == 0) {
			$this->error = "<span class='validation-error'>Username/Password invalid</span>";
		} else if($results[0]['Active'] == 0) {
			$this->error = "<span class='validation-error'>This email address has not been confimred! Please check your email and follow the confirmation link.</span>";
		} else {
			$encrypted = $results[0]['UserPassword'];
			$userId = $results[0]['UserId'];
			$pastIps = $this->logIpAddress($results[0]['RecentIps']);
			$authentication = $this->generateJsonAuthentication($this->email);
			$status = $this->updateUser($pastIps, $authentication, $userId);
			
			if($status == 1) {
				$this->verifyPassword($encrypted, $userId, $authentication);
			} else {
				$this->error = "<span class='validation-error'>Something went wrong! Please try again later.</span>";
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