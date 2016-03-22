<?php

function ConnectDb() {
	return new PDO('mysql:host=localhost;dbname=notes;', 'root', '');
}

?>