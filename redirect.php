<?php

require 'sitefunctions.php';


if (count($_POST) > 0) {
    $from = filterURL($_GET['from']);

    if ($from == 'login') {

        $found = false; // assume not found

        $email = trim($_POST['email']);
        $pass = trim($_POST['password']);

        if (checkEmail($email)) {

            $found = findUser($email, $pass);

            if($found)
            {
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                setcookie('success', 'Welcome, '.getUsername($_SESSION['email']).'!');
                header('Location: index.php');
                exit();
            }

        }

    setcookie('error_message', 'Login not found! Try again.');
    header('Location: index.php');
    exit();

    } elseif ($from == 'signup') {

        if(checkSignUp($_POST) && saveUser($_POST)) {

            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = strtolower(trim($_POST['email']));
            insert_user_product_list($_SESSION['email']);


            setcookie('success', 'Welcome, '.getUsername($_SESSION['email']).'!');
            header('Location: index.php');
            exit();
        }

        setcookie('error_message', 'Unable to sign up at this time.');
        header('Location: index.php');
        exit();

    } elseif ($from == 'newItem') {
        session_start();
        if (isset($_SESSION['loggedin'])) {
            if(count($_FILES) > 0) {

                $check = checkPost($_FILES);
                if ($check == 1) {
                    $success = saveProfile($_FILES, $_POST, $_SESSION['email'], 'jpg');
                } elseif ($check == 2) {
                    $success = saveProfile($_FILES, $_POST, $_SESSION['email'], 'png');
                }

                if ($success) {
                    setcookie('success', 'New Item posted!');
                    header('Location: index.php');
                    exit();
                }

            }
            header('Location: index.php');
            exit();
        }
        setcookie('error_message', 'Must be Logged in to upload items.');
        header('Location: index.php');
        exit();

    }
}
