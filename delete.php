<?php
    session_start();
    include 'DBfunctions.php';

    if (!isset($_SESSION['loggedin'])) {
        setcookie('error_message', 'You need to login first to do that.');
        header('Location: index.php');
        exit();
    }

    // only alphanumeric allowed
    $product_id = preg_replace("/[^a-z0-9]/i", '', $_GET['product_id']);

    $results = singleItem($product_id);

    if (mysqli_num_rows($results) > 0 ) {
        while( $row = mysqli_fetch_array($results)) {

            if ($row['email'] != $_SESSION['email']) {
                setcookie('error_message', 'You can only delete your own items.');
                header('Location: index.php');
                exit();
            } else {
                deleteFromProductsTbl($product_id);
                deleteFromUserProductsListTbl($product_id);
            }

            disconnectDb();
            setcookie('success', 'Item has been deleted.');
            header('Location: index.php');
            exit();
        }
    }

    
    

    

?>