<?php

class Note {
	public $db = null;
	public $email = '';
	public $password = '';
	public $id = 0;

	public function __construct() {
		require_once 'db-connect.inc.php';
		$this->db = Database::ConnectDb();
		echo 'Note created!';
	}
}

?>