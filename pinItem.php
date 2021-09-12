<?php
session_start();

include 'sitefunctions.php';

if (!isset($_SESSION['loggedin'])) {
    setcookie('error_message', 'You must be logged in to do this.');
    header('Location: index.php');
    exit();
}

$from = filterURL($_GET['from']);

if ($from == 'add') {
    addPinItem($_GET['email'], $_GET['product_id']);
    setcookie('success', 'You have pinned an item');
    header('Location: index.php');
    exit();
} elseif ($from == 'remove') {
    removePinItem($_GET['email'], $_GET['product_id']);
    setcookie('success', 'You have un-pinned an item');
    header('Location: index.php');
    exit();
}


?>