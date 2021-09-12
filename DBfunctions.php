<?php

function connectToDb() {
    $dbusername = 'root';
    $dbpassword = '';
    $port     = '3306';
    $database = 'finalassignment3015';
    $host     = 'localhost';

    return mysqli_connect($host, $dbusername, $dbpassword, $database, $port);
}

function disconnectDb() {
    $link = connectToDb();
    mysqli_close($link);
}

function allItems() {

    $link = connectToDb();

    if ($link != false) {

        $query = "SELECT u.user_id, u.firstname, u.lastname, u.email, p.product_id, p.description,  p.picture, p.title, p.price, p.expiry 
                FROM users AS u INNER JOIN products AS p ON u.user_id = p.user_id"; 
        
        return mysqli_query($link, $query);              
    } 
    
}

function recentItems($data) {

    $idArr = $data;

    $recentItems= implode(", ",$idArr);

    $link = connectToDb();

    if ($link != false) {

        $query = "SELECT u.user_id, u.firstname, u.lastname, u.email, p.product_id, p.description,  p.picture, p.title, p.price 
                FROM users AS u INNER JOIN products AS p ON u.user_id = p.user_id
                WHERE product_id in ($recentItems)
                ORDER BY u.lastname,
                p.price"; 
        
        return mysqli_query($link, $query);              
    } 
    
}

function userList($email) {

    $link = connectToDb();

    if ($link != false) {

        $query = "SELECT user_id, firstname, lastname, product_author, product_id, description, picture, title, price, votes, pinned, expiry 
                FROM user_products_list
                WHERE email='".$email."'
                ORDER BY pinned desc"; 
        
        return mysqli_query($link, $query);              
    } 
    
}

function searchAllItems($term) {

    $link = connectToDb();

    if ($link != false) {
        
        $query = "SELECT 
                    u.firstname, u.lastname, u.email, p.product_id, p.description,  p.picture, p.title, p.price, p.expiry 
                FROM users AS u 
                INNER JOIN products AS p ON u.user_id = p.user_id
                WHERE 
                    CONCAT(title,description) LIKE '%".$term."%'";
        
        return mysqli_query($link, $query);              
    } 
    
}

function searchUserItems($term, $email) {

    $link = connectToDb();

    if ($link != false) {
        
        $query = "SELECT user_id, firstname, lastname, product_author, product_id, description, picture, title, price, votes, pinned, expiry 
                FROM user_products_list
                WHERE 
                    CONCAT(title,description) LIKE '%".$term."%' AND email='".$email."'
                ORDER BY pinned desc"; 
        
        return mysqli_query($link, $query);              
    } 
    
}


function singleItem($id) {

    $link = connectToDb();

    if ($link != false) {

        $query = "SELECT u.firstname, u.lastname, u.email, p.product_id, p.description,  p.picture, p.title, p.price 
                FROM users AS u INNER JOIN products AS p ON u.user_id = p.user_id
                WHERE product_id ='".$id."'"; 
        
        return mysqli_query($link, $query);              
    } 
    
}

function singleItemByPic($pic) {

    $link = connectToDb();

    if ($link != false) {

        $query = "SELECT u.firstname, u.lastname, u.email, p.product_id, p.description,  p.picture, p.title, p.price 
                FROM users AS u INNER JOIN products AS p ON u.user_id = p.user_id
                WHERE picture ='".$pic."'"; 
        
        return mysqli_query($link, $query);              
    } 
    
}


/**
 * $table, optional $whereClause AND $value
 * @param $table, optional $whereClause AND $value
 */
function selectQuery($table, $whereClause = false, $value = false) {

    $link = connectToDb();

    if ($link != false) {
        
        if ($whereClause !== false) {
            
            $query = "SELECT * FROM $table WHERE $whereClause='".$value."'";
            
            return mysqli_query($link, $query);   
            
        } else if ($whereClause == false) {
            
            $query = "SELECT * FROM $table";
            
            return mysqli_query($link, $query);
        }
        
    }
    
}

function getUser_ID($email) {

    $link = connectToDb();

    if ($link != false) {
            
        $query = "SELECT user_id FROM users WHERE email='".$email."'";
        
        $results = mysqli_query($link, $query);

        if (mysqli_num_rows($results) > 0) {
            while($row = mysqli_fetch_array($results)) {
                return $row['user_id'];
            }
        } 
    }

    disconnectDb();
}

function getFirst_Name($email) {

    $link = connectToDb();

    if ($link != false) {
            
        $query = "SELECT firstname FROM users WHERE email='".$email."'";
        
        $results = mysqli_query($link, $query);

        if (mysqli_num_rows($results) > 0) {
            while($row = mysqli_fetch_array($results)) {
                return $row['firstname'];
            }
        } 
    }

    disconnectDb();
}

function getLast_Name($email) {

    $link = connectToDb();

    if ($link != false) {
            
        $query = "SELECT lastname FROM users WHERE email='".$email."'";
        
        $results = mysqli_query($link, $query);

        if (mysqli_num_rows($results) > 0) {
            while($row = mysqli_fetch_array($results)) {
                return $row['lastname'];
            }
        } 
    }

    disconnectDb();
}

function deleteFromProductsTbl($id) {

    $link = connectToDb();

    if ($link != false) {

        $query   = 'delete from products where product_id = "'.$id.'"';
        
        $queryResults = mysqli_query($link, $query);   
    }

    if ($queryResults) {
        mysqli_close($link);
    }

}

function deleteFromUserProductsListTbl($id) {

    $link = connectToDb();

    if ($link != false) {

        $query   = 'delete from user_products_list where product_id = "'.$id.'"';
        
        $queryResults = mysqli_query($link, $query);   
    }

    if ($queryResults) {
        mysqli_close($link);
    }

}

/**
 * $title, $price, $desc, $picture, $user_id required
 * returns success true or false after insert
 * @param $title, $price, $desc, $picture, $user_id 
 */
function insertProduct($title, $price, $desc, $picture, $expiry, $user_id) {

    $link = connectToDb();

    if ($link != false) {

        $query = "INSERT INTO products (title, price, description, picture, expiry, user_id)
                VALUES ('".$title."', '".$price."', '".$desc."', '".$picture."', '".$expiry."', '".$user_id."')";
    
        $queryResults = mysqli_query($link, $query);
    }

    if ($queryResults) {
        mysqli_close($link);
    }

}

function insert_product_list($product_id, $user_id, $title, $product_author, $desc, $picture, $firstname, $lastname, $price, $email, $votes) {

    $link = connectToDb();

    if ($link != false) {

        $query = "INSERT INTO user_products_list (product_id, user_id, title, product_author, description, picture, firstname, lastname, price, email, votes, pinned)
                VALUES ('".$product_id."', '".$user_id."', '".$title."', '".$product_author."', '".$desc."',
                 '".$picture."', '".$firstname."', '".$lastname."', '".$price."', '".$email."', '".$votes."', false)";
    
        $queryResults = mysqli_query($link, $query);

    }

    if ($queryResults) {
        mysqli_close($link);
    }

}

function deletePinItem($email, $product_id) {

    $link = connectToDb();

    if ($link != false) {

        $query = "UPDATE user_products_list SET pinned=0 WHERE email = '".$email."' AND product_id = $product_id";
    
        $queryResults = mysqli_query($link, $query);

    }

    if ($queryResults) {
        mysqli_close($link);
    }

}

function insertPinItem($email, $product_id) {

    $link = connectToDb();

    if ($link != false) {

        $query = "UPDATE user_products_list SET pinned=1 WHERE email = '".$email."' AND product_id = $product_id";
    
        $queryResults = mysqli_query($link, $query);

    }

    if ($queryResults) {
        mysqli_close($link);
    } else {
        echo "didn't update";
    }

}

function downVoteItem($product_id) {

    $link = connectToDb();

    if ($link != false) {

    $votesQuery = "SELECT votes 
                FROM user_products_list
                WHERE product_id='".$product_id."'";
    
    $votesResult = mysqli_query($link, $votesQuery);

    if (mysqli_num_rows($votesResult) > 0) {
        while ($row = mysqli_fetch_array($votesResult)) {
            $votes = $row['votes'];
        }
    } 

    $votes = $votes-1;

    $query = "UPDATE user_products_list SET votes=$votes WHERE product_id = $product_id";

    mysqli_query($link, $query);

    }

    if ($votesResult) {
        mysqli_close($link);
    }

}

function usersVote($email) {

    $link = connectToDb();

    if ($link != false) {

        $votesQuery = "SELECT downvotes 
                    FROM users
                    WHERE email='".$email."'";
        
        $votesResult = mysqli_query($link, $votesQuery);

        if (mysqli_num_rows($votesResult) > 0) {
            while ($row = mysqli_fetch_array($votesResult)) {
                $votes = $row['downvotes'];
            }
        } 

        if ($votesResult) {
            mysqli_close($link);
        }

        return $votes;

    }

}

function setUserVotes($email) {

    $link = connectToDb();

    $query = "UPDATE users SET downvotes=false WHERE email = '".$email."'";

    mysqli_query($link, $query);
}
