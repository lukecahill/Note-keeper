<?php

class Database {
	public static function ConnectDb() {
		$host = 'localhost';
		$db = 'notes';
		$user = 'root';
		$pass = '';
		$charset = 'utf8';
		
		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		
		$pdo = new PDO($dsn, $user, $pass);
		//$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	}
}

?>