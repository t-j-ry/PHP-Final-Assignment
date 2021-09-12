<?php 

session_start();


require 'sitefunctions.php';

$id = filterURL($_GET['id']);

if (!isset($_SESSION['recently-viewed'])) {
    $_SESSION['recently-viewed'] = [];
} 

if (count($_SESSION['recently-viewed']) < 4) {
    array_push($_SESSION['recently-viewed'], $id);
} else {
        array_shift($_SESSION['recently-viewed']);
        array_push($_SESSION['recently-viewed'], $id);
}

foreach ($_SESSION['recently-viewed'] as $key => $item) {
    setcookie('recently-viewed['.$key.']', $item);
}


if (!isset($_SESSION['email'])) {
    $email = '';
} elseif (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

if(!isset($_SESSION['loggedin'])) {
    $loggedIn = false;
    echo 
    '<div class="alert alert-warning text-center">
        No one logged in.
    </div>'; 
} elseif (isset($_SESSION['loggedin'])) {
    $loggedIn = true;
}


?>


<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper">

    <div class="container">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h1 class="login-panel text-center text-muted">
                    COMP 3015 Final Project
                </h1>
                <hr/>
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-3 col-md-6">
            <?php
                displayAnItem($id, $loggedIn, $email, true);
            ?>
            </div>
        </div>

    </div>

</div>
</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
