<?php

class Authentication {
	public $email = '';
	public $password = '';
	public $db = null;

	function __construct($email, $password) {
		require_once 'includes/db-connect.inc.php';
		$this->db = Database::ConnectDb();
		$this->email = $email;
		$this->password = $password;
	}	
}

?>