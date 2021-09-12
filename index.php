<?php 

session_start();

require 'sitefunctions.php';

if(isset($_COOKIE['error_message'])) {
    echo '<div class="alert alert-danger text-center">'
        . $_COOKIE['error_message'] .
        '</div>';

    setcookie('error_message', null, time() - 3600);
}

if(isset($_COOKIE['success'])) {
    echo '<div class="alert alert-success text-center">'
        . $_COOKIE['success'] .
        '</div>';

    setcookie('success', null, time() - 3600);
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
            <div class="col-md-6 col-md-offset-3">
                <?php 
                    if ($loggedIn) {
                        echo 
                        '<button class="btn btn-default" data-toggle="modal" data-target="#newItem"><i class="fa fa-photo"></i> New Item</button>
                        <a href="logout.php" class="btn btn-default pull-right"><i class="fa fa-sign-out"> </i> Logout</a>';
                    }
                    
                    if (!$loggedIn) {
                        echo
                        '<a href="#" class="btn btn-default pull-right" data-toggle="modal" data-target="#login"><i class="fa fa-sign-in"> </i> Login</a>
                        <a href="#" class="btn btn-default pull-right" data-toggle="modal" data-target="#signup"><i class="fa fa-user"> </i> Sign Up</a>';
                    }
                
                
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <h2 class="login-panel text-muted">
                    Recently Viewed
                </h2>
                <hr/>
            </div>
        </div>
        <div class="row">
            <?php 
                if (isset($_COOKIE['recently-viewed'])) {
                    displayRecentItems($loggedIn, $email, $_COOKIE['recently-viewed']);
                }

            ?>

        </div>

        <div class="row">
            <div class="col-md-3">
                <h2 class="login-panel text-muted">
                    Items For Sale
                </h2>
                <hr/>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                    <form class="form-inline" method="GET" action="index.php">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-search"></i></div>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Search"
                                    name="search"
                                />
                            </div>
                        </div>
                        <input type="submit" class="btn btn-default" value="Search"/>
                        <button class="btn btn-default" data-toggle="tooltip" title="Shareable Link!"><i class="fa fa-share"></i></button>
                    </form>
                <br/>
            </div>
        </div>

        <div class="row">
            <?php

                if (!isset($_GET['search'])) {
                    $searchTerm = "";
                } else {
                    $searchTerm = filterURL($_GET['search']);
                }

                if (isset($_SESSION['loggedin'])) {
                    if ($searchTerm != '') {
                        displayUserItemsList($loggedIn, $email, $searchTerm);
                    } else {
                        displayUserItemsList($loggedIn, $email);
                    }
                } elseif (!isset($_SESSION['loggedin'])) {
                    if ($searchTerm != '') {
                            displayAllItems($loggedIn, $email, $searchTerm);
                        } else {
                            displayAllItems($loggedIn, $email);
                        }
                }   
            ?>
        </div>
        
    </div>

</div>

<div id="login" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="redirect.php?from=login">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Login</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" 
                        type="text"
                        value=""
                        name="email"
                        placeholder="example@example.com"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" 
                        type="password"
                        value=""
                        name="password"
                        autofocus
                    >
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Login!"/>
            </div>
        </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="newItem" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="redirect.php?from=newItem" enctype="multipart/form-data">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">New Item</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
                    <input class="form-control" 
                        type="text"
                        value=""
                        name="title"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input class="form-control" 
                        type="text"
                        value=""
                        name="price"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input class="form-control" 
                        type="text"
                        value=""
                        name="desc"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Picture</label>
                    <input class="form-control" type="file" name="picture">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Post Item!"/>
            </div>
        </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="signup" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="redirect.php?from=signup">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Sign Up</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>First Name</label>
                    <input class="form-control" 
                        type="text"
                        value=""
                        name="firstname"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input class="form-control" 
                        type="text"
                        value=""
                        name="lastname"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" 
                        type="text"
                        value=""
                        name="email"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" 
                        type="password"
                        value=""
                        name="password"
                        autofocus
                    >
                </div>
                <div class="form-group">
                    <label>Verify Password</label>
                    <input class="form-control" 
                        type="password"
                        value=""
                        name="verify_password"
                        autofocus
                    >
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary" value="Sign Up!"/>
            </div>
        </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
</html>
