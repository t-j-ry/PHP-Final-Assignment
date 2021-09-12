<?php 

require 'DBfunctions.php';
require 'components.php';

define('SALT', 'a_little_salt_for_this_app');
define('FILE_SIZE_LIMIT', 4000000);

function displayAnItem($id, $loggedIn, $email, $backbtn) {

    $results = singleItem($id);
    
    $pinned = false;

    if (mysqli_num_rows($results) > 0) {
        while( $row = mysqli_fetch_array($results)  )
        {
            $username = $row['firstname'].' '.$row['lastname'];
            singleProduct(
                $row['product_id'], 
                $row['title'],
                $row['description'],
                $row['picture'],
                $username,
                $row['price'], 
                $row['email'], 
                $loggedIn, 
                $email,
                $pinned,
                $backbtn
            );
        }
    }

    disconnectDb();
}

function displayUserItemsList($loggedIn, $email, $search = false) {

    if (!$search) {
        $results = userList($email);
    } else {
        $results = searchUserItems($search, $email);
    }

    if (mysqli_num_rows($results) > 0) {
        while($row = mysqli_fetch_array($results)) {
            // change to get author name
            $username = getFirst_Name($row['product_author']).' '.getLast_Name($row['product_author']);

            if (time() < $row['expiry'] && $row['votes'] > 0) {
                card(
                    $row['product_id'], 
                    $row['title'],
                    $row['description'],
                    $row['picture'],
                    $username,
                    $row['price'], 
                    $row['product_author'], 
                    $loggedIn, 
                    $email,
                    $row['pinned']
                );
            }
             elseif (time() > $row['expiry'] || $row['votes'] < 1) {
                deleteFromUserProductsListTbl($row['product_id']);
                deleteFromProductsTbl($row['product_id']);
            } 
                
            
        } 
    }

    disconnectDb();
}


function displayAllItems($loggedIn, $email, $search = false) {

    if (!$search) {
        $results = allItems();
    } else {
        $results = searchAllItems($search);
    }

    $pinned = false;

    if (mysqli_num_rows($results) > 0) {
        while($row = mysqli_fetch_array($results)) {
            if (time() < $row['expiry']) {               
                $username = $row['firstname'].' '.$row['lastname'];
                card(
                    $row['product_id'], 
                    $row['title'],
                    $row['description'],
                    $row['picture'],
                    $username,
                    $row['price'], 
                    $row['email'], 
                    $loggedIn, 
                    $email,
                    $pinned
                );
            } 
            elseif(time() > $row['expiry']) 
            {
                deleteFromUserProductsListTbl($row['product_id']);
                deleteFromProductsTbl($row['product_id']);
            }
        } 
    } else {
        echo "no results";
    }

    disconnectDb();
}

function displaySearchedItems($loggedIn, $email) {
    
    $results = searchAllItems();

    if (mysqli_num_rows($results) > 0) {
        while($row = mysqli_fetch_array($results)) {
            $username = $row['firstname'].' '.$row['lastname'];
            
            card(
                $row['product_id'], 
                $row['title'],
                $row['description'],
                $row['picture'],
                $username,
                $row['price'], 
                $row['email'], 
                $loggedIn, 
                $email
            );
        }
    } else {
        echo "no results";
    }

    disconnectDb();
}

function displayRecentItems($loggedIn, $email, $data) {

    $results = recentItems($data);

    $pinned = false;

    if (mysqli_num_rows($results) > 0) {
        while($row = mysqli_fetch_array($results)) {
            $username = $row['firstname'].' '.$row['lastname'];
            card(
                $row['product_id'], 
                $row['title'],
                $row['description'],
                $row['picture'],
                $username,
                $row['price'], 
                $row['email'], 
                $loggedIn, 
                $email,
                $pinned
            );
        } 
    } else {
        echo "no results";
    }

    disconnectDb();
}


function findUser($user, $pass) {
    $found = false;

    $link = connectToDb();
    $hash = md5($pass . SALT);

    $query   = 'select * from users where email = "'.$user.'" and password = "'.$hash.'"';
    $results = mysqli_query($link, $query);

    if (mysqli_fetch_array($results))
    {
        $found = true;
    }

    mysqli_close($link);
    return $found;
}

function getUsername($email) {
    $results = selectQuery('users', 'email', $email);

    if (mysqli_num_rows($results) > 0) {
        while( $row = mysqli_fetch_array($results)  )
        {
            return $name = $row['firstname'].' '.$row['lastname'];
        }
    } else {
        return "no results";
    }

    disconnectDb();
}

function checkEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function saveUser($data)
{
    $email   = strtolower(trim($data['email']));
    $password   = md5($data['password']. SALT);
    $firstname = strtolower($data['firstname']);
    $lastname = strtolower($data['lastname']);

    $link    = connectToDb();
    $query   = 'insert into users(firstname, lastname, email, password, downvotes) values("'.$firstname.'","'.$lastname.'","'.$email.'","'.$password.'",true)';
    $success = mysqli_query($link, $query); // returns true on insert statements

    mysqli_close($link);
    return $success;
}

function checkSignUp($data)
{
    $valid = true;

    // if any of the fields are missing
    if( trim($data['firstname'])        == '' ||
        trim($data['lastname'])        == '' ||
        trim($data['email'])        == '' ||
        trim($data['password'])        == '' ||
        trim($data['verify_password']) == '')
    {
        $valid = false;
    }
    // Firstname reg expression
    elseif (!preg_match("/^([a-zA-Z' ]+)$/",$data['firstname'])) 
    {
        $valid = false;
    }
    // Lastname reg expression
    elseif (!preg_match("/^([a-zA-Z' ]+)$/",$data['lastname'])) 
    {
        $valid = false;
    }
    elseif(!checkEmail(trim($data['email'])))
    {
        $valid = false;
    }
    elseif(!preg_match('/((?=.*[a-z])(?=.*[0-9])(?=.*[!?|@])){8}/', trim($data['password'])))
    {
        $valid = false;
    }
    elseif($data['password'] != $data['verify_password'])
    {
        $valid = false;
    }

    return $valid;
}

function checkPost($file)
{
    if($file['picture']['size'] < FILE_SIZE_LIMIT && $file['picture']['type'] == 'image/jpeg') {
        return 1;
    } elseif ($file['picture']['size'] < FILE_SIZE_LIMIT && $file['picture']['type'] == 'image/png') {
        return 2;
    }

    return 'Unable to upload item picture!';
}

/**
 * @param $username
 * @param $file
 * @return bool
 */
function saveProfile($file, $data, $email, $filetype) {

    $valid = true;
    $picture = md5($email.time());

    $results = selectQuery('users', 'email', $email);

    if (mysqli_num_rows($results) > 0) {
        while($row = mysqli_fetch_array($results)) {
            $id = $row['user_id'];
        }
    } 
    else {
        $valid = false;
    }

    if( trim($data['title'])        == '' ||
        trim($data['price'])        == '' ||
        trim($data['desc'])        == '' ||
        trim($picture)        == '' ||
        trim($id) == '')
    {
        $valid = false;
    }
    // Title reg expression
    elseif (!preg_match("/^([a-zA-Z' ]+)$/", $data['title'])) 
    {
        setcookie('error_message', 'Description failed');
        $valid = false;
    }
    // Description reg expression
    elseif (!preg_match('/[A-Za-z0-9_~\-!@#\$%\^&\*\(\)\,\.\? ]+$/', $data['desc'])) 
    {
        setcookie('error_message', 'Description failed');
        $valid = false;
    }
    // Added price reg expression 
    elseif (!preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $data['price'] )) 
    {
        setcookie('error_message', 'Price failed');
        $valid = false;
    }

    $expiryTime = time() + 3600;

    if($valid) {
        $moved = move_uploaded_file($file['picture']['tmp_name'], 'products/'.$picture.'.'.$filetype);
        if ($moved) {
            insertProduct($data['title'],$data['price'],$data['desc'],$picture.'.'.$filetype,$expiryTime,$id);

            $link = connectToDb();

            $picResults = singleItemByPic($picture.'.'.$filetype);

            if (mysqli_num_rows($picResults) > 0) {
                while( $row = mysqli_fetch_array($picResults)  )
                {
                    $title = $row['title'];
        
                    $desc = $row['description'];
        
                    $product_author = $row['email'];
        
                    $product_id = $row['product_id'];
        
                    $picture = $row['picture'];
        
                    $price = $row['price'];
        
                    $usersResults = selectQuery('users');
        
                    if (mysqli_num_rows($usersResults) > 0) {
                        while( $row = mysqli_fetch_array($usersResults)  )
                        {
                            $userID = $row['user_id'];
        
                            $firstname = $row['firstname'];
        
                            $lastname = $row['lastname'];
        
                            $email = $row['email'];
        
                            $query = "INSERT INTO user_products_list 
                                (product_id, user_id, title, product_author, description, picture, firstname, lastname, price, email, votes, pinned, expiry)
                            VALUES 
                                ('".$product_id."', '".$userID."', '".$title."', '".$product_author."', '".$desc."',
                                '".$picture."', '".$firstname."', '".$lastname."', '".$price."', '".$email."', 5, false, '".$expiryTime."')";
                            mysqli_query($link, $query);
                            
                        }
                    } else {
                        echo "no results";
                    }
                    
                }
            } else {
                echo "no results";
            }
           
        }
    }

    return $valid;
}

function filterURL($url)
{
    // if it's not alphanumeric, replace it with an empty string
    return preg_replace("/[^a-z0-9]/i", '', $url);
}



function insert_user_product_list($email) {

    $userID = getUser_ID($email);

    $firstname = getFirst_Name($email);

    $lastname = getLast_Name($email);

    $results = allItems();

    $link = connectToDb();

    if (mysqli_num_rows($results) > 0) {
        while( $row = mysqli_fetch_array($results)  )
        {
            $query = "INSERT INTO user_products_list 
                (product_id, user_id, title, product_author, description, picture, firstname, lastname, price, email, votes, pinned, expiry)
            VALUES 
                ('".$row['product_id']."', '".$userID."', '".$row['title']."', '".$row['email']."', '".$row['description']."',
                 '".$row['picture']."', '".$firstname."', '".$lastname."', '".$row['price']."', '".$email."', 5, false, '".$row['expiry']."')";
            mysqli_query($link, $query);
        }
    } else {
        echo "no results";
    }

    disconnectDb();

}

function addPinItem($product_id, $email) {

    insertPinItem($product_id, $email);

}

function removePinItem($product_id, $email) {

    deletePinItem($product_id, $email);

}

?>