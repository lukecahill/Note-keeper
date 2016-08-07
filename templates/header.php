<?php

session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['userId']) || !isset($_SESSION['authentication'])) {
	die(header('Location: login.php'));
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>
		Note Keeper
	</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
	
	<link href='//fonts.googleapis.com/css?family=Droid+Sans:700,400' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

	<?php include 'links.php'; ?>

	<div class="container-fluid">