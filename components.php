<?php


function card($product_id, $title, $desc, $picture, $user, $price, $authorEmail, $loggedIn, $email, $pinned) {
    echo 
    '<div class="col-md-3">
        <div class="panel panel-'.isPinned($pinned).'">'.
        productHeader($title, $authorEmail, $loggedIn, $email, $product_id, $pinned).
        productBody($desc,$picture, $loggedIn, $product_id).
        productFooter($user, $price, $authorEmail)
        .'</div>
    </div>';
}


function productHeader($title, $authorEmail, $loggedIn, $email, $product_id, $pinned) {
    return 
    '<div class="panel-heading">'.
            pinBtn($loggedIn, $product_id, $email, $pinned)
        .'<span>'.
            $title
        .'</span>
        <span class="pull-right text-muted">'.
            deleteBtn($authorEmail, $email, $product_id)
        .'</span>
    </div>';    
}

function deleteBtn($authorEmail, $email, $product_id) {

    if ($authorEmail == $email) {
        return
        '<a class="" href="delete.php?product_id='.$product_id.'" data-toggle="tooltip" title="Delete item">
            <i class="fa fa-trash"></i>
        </a>';
    }
    return '';
    
}

function pinBtn($loggedIn, $product_id, $email, $pinned) {
    if ($loggedIn) {
        if (isPinned($pinned) == 'warning') {
            return 
            '<a class="" href="pinItem.php?from=remove&product_id='.$product_id.'&email='.$email.'" data-toggle="tooltip" title="unPin item">
                <i class="fa fa-dot-circle-o"></i>
            </a>';
        } else {
            return 
            '<a class="" href="pinItem.php?from=add&product_id='.$product_id.'&email='.$email.'" data-toggle="tooltip" title="Pin item">
                <i class="fa fa-thumb-tack"></i>
            </a>';
        }
    }
    return '';
}

function productBody($desc, $picture, $loggedIn, $product_id) {
    return

    '<div class="panel-body text-center">
        <p>
            <a href="product.php?id='.$product_id.'">
                <img class="img-rounded img-thumbnail" src="products/'.$picture.'"/>
            </a>
        </p>
        <p class="text-muted text-justify">'.
            $desc
        .'</p>'.
        thumbDownBtn($loggedIn, $product_id)  
    .'</div>';
}

function thumbDownBtn($loggedIn, $product_id) {
    if ($loggedIn) {
        return
        '<a class="pull-left" href="downvote.php?product_id='.$product_id.'" data-toggle="tooltip" title="Downvote item">
            <i class="fa fa-thumbs-down"></i>
        </a>';
    }
    return '';
}

function productFooter($user, $price, $authorEmail) {
    return

    '<div class="panel-footer ">
        <span><a href="mailto:'.$authorEmail.'" data-toggle="tooltip" title="Email seller"><i class="fa fa-envelope"></i> '.$user.'</a></span>
        <span class="pull-right">$'.$price.'</span>
    </div>';

}

function singleProduct($product_id, $title, $desc, $picture, $user, $price, $authorEmail, $loggedIn, $email, $pinned, $backbtn) {
    echo        backbtn($backbtn).'
                <div class="panel panel-info">'.
                    productHeader($title, $authorEmail, $loggedIn, $email, $product_id, $pinned).
                    productBody($desc,$picture, $loggedIn, $product_id).
                    productFooter($user, $price, $email)
                .'</div>';
}

function backbtn($backbtn) {
    if ($backbtn) {
        return '
        <div>
            <p>
                <a class="btn btn-default" href="index.php">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </p>
        </div>
        ';
    }
}

function isPinned($pinned) {

    if (!$pinned) {
        return 'info';
    } else {
        return 'warning';
    }
    

}