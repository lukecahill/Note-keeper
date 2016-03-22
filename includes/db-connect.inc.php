<?php

function ConnectDb() {
	return new PDO('mysql:host=localhost;dbname=notes;charset=utf8', 'root', '');
}

?>