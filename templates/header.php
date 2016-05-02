<?php

session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['userId'])) {
	die(header('Location: login.php'));
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>
		Notes
	</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>

	<?php include 'links.php'; ?>

	<div class="container-fluid">