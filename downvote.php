<?php
    session_start();
    include 'DBfunctions.php';

    if (!isset($_SESSION['loggedin'])) {
        setcookie('error_message', 'You need to login first to do that.');
        header('Location: index.php');
        exit();
    }

    $email = $_SESSION['email'];
    
    //only alpha numeric allowed
    $product_id = preg_replace("/[^a-z0-9]/i", '', $_GET['product_id']);

    if (usersVote($email)) {
        downVoteItem($product_id);
        setUserVotes($email);
        setcookie('success', 'You have down voted a product.');
        header('Location: index.php');
        exit();
    } 

    setcookie('error_message', 'You have no votes left.');
    header('Location: index.php');
    exit();