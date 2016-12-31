<?php
/**
* Creates a PDO object for database interaction. 
* This is a static class
* Example usage:
* Database::ConnectDb();
*
* @package  Note Keeper
* @author   Luke Cahill
* @access   public
*/
class Database {
	public static function ConnectDb() {
		$host = 'localhost';
		$db = 'note_keeper';
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