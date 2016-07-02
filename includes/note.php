<?php

class Note {
	public $db = null;
	public $email = '';
	public $password = '';
	public $id = 0;

	function __construct() {
		require_once 'db-connect.inc.php';
		$this->db = Database::ConnectDb();
	}
}

?>