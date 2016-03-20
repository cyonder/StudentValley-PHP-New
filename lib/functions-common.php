<?php

/****************************************
 ****************************************
FUNCTION LIST
1. user_exist();
2. return_user_id();
3. return_user_picture_id();
4. return_title();
5. request_exist();
6. are_they_connected();
7. multiple_submission();
 ****************************************
 ***************************************/

/** This function checks if the user user is exist in database or not.
 * If exist return true, if not return false.
 */
function user_exist($dbConnection, $user_email){
    $query = "SELECT * FROM USERS ";
    $query .= "WHERE USER_EMAIL = ";
    $query .= "'$user_email'";

    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
    $num_rows = mysqli_num_rows($result);

    if($num_rows != 0){
        return TRUE;
    }else{
        return FALSE;
    }
}

/** This function returns user id if you pass email to it
 */
function return_user_id($dbConnection, $email){
    $query = "SELECT USER_ID FROM USERS ";
    $query .= "WHERE USER_EMAIL = ";
    $query .= "'$email'";
    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

    while($data = mysqli_fetch_assoc($result)){
        $user_id = $data['USER_ID'];
    }
    return $user_id;
}

/** This function returns user picture id if you pass email to it
 */
function return_user_picture_id($dbConnection, $email){
    $query = "SELECT USER_PICTURE_ID FROM USERS ";
    $query .= "WHERE USER_EMAIL = ";
    $query .= "'$email'";
    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

    while($data = mysqli_fetch_assoc($result)){
        $user_picture_id = $data['USER_PICTURE_ID'];
    }
    return $user_picture_id;
}

/** IMPORTANT! This function is designed for user profile pages only. DO NOT USE SOMEWHERE ELSE!
 * This function returns the title of profile page.
 * Which is user's first and last name.
 */
function return_title($dbConnection, $id, $page){
    if($page == "/profile.php"){
        $query = "SELECT USER_FIRST_NAME, USER_LAST_NAME FROM USERS WHERE USER_ID = '$id'";
        $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        if(mysqli_num_rows($result) == 1){
            while($data = mysqli_fetch_assoc($result)){
                $first_name = $data['USER_FIRST_NAME'];
                $last_name = $data['USER_LAST_NAME'];
            }
            $title = "$first_name" . " " . "$last_name";
        }else{
            $title = "Profile Unavailable";
        }
    }else if(substr($page, 1, 5) == "group"){
        if(substr($page, 7, -4) == "group"){ // If page name is group, we don't wanna put it in title.
            $title_extension = NULL;
            $seperator = NULL;
        }else{
            $seperator = " - ";
            $title_extension = substr($page, 7, -4); // Get the page name between /group/ and .php for title use.
        }
        $query = "SELECT GROUP_NAME FROM GROUPS WHERE GROUP_ID = '$id'";
        $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        if(mysqli_num_rows($result) == 1){
            while($data = mysqli_fetch_assoc($result)){
                $group_name = $data['GROUP_NAME'];
            }
            $title = $group_name . $seperator . ucfirst($title_extension); // Make the first letter upper case.
        }else{
            $title = "Group Unavailable";
        }
    }else if(substr($page, 1, 4) == "book"){
        $query = "SELECT BOOK_TITLE FROM BOOKS WHERE BOOK_ID = '$id'";
        $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        if(mysqli_num_rows($result) == 1){
            while($data = mysqli_fetch_assoc($result)){
                $book_title = $data['BOOK_TITLE'];
            }
            $title = $book_title;
        }else{
            $title = "Book Unavailable";
        }
    }
    return $title;
}

/** This function will return true if the request is sent already.
 *
 * This function used for:
 *
 * 1) To prevents database getting multiple request submissions for the same request.
 * 2) To not let other user to connect each other without even sending a request.
 * 3) To manipulate the connection buttons in profile page.
 *
 * */
function request_exist($dbConnection, $request_from, $request_to){
    $query = "SELECT * FROM REQUESTS ";
    $query .= "WHERE REQUEST_FROM = ";
    $query .= "'$request_from' AND ";
    $query .= "REQUEST_TO = ";
    $query .= "'$request_to'";

    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
    $num_rows = mysqli_num_rows($result);

    if($num_rows != 0){
        return TRUE;
    }else{
        return FALSE;
    }
}

/** This function will return true if users are connected with each other */
function are_they_connected($dbConnection, $user1, $user2){
    $result1 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user1'");
    $data1 = mysqli_fetch_assoc($result1);
    $user1_connections = $data1['USER_CONNECTIONS']; //This is all the connections user1 has.
    $user1_each_connection = explode(",", $user1_connections); //This is every single connection user1 has. (In array).

    $result2 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user2'");
    $data2 = mysqli_fetch_assoc($result2);
    $user2_connections = $data2['USER_CONNECTIONS']; //This is all the connections user2 has.
    $user2_each_connection = explode(",", $user2_connections); //This is every single connection user2 has. (In array).

    if(in_array($user2, $user1_each_connection) && in_array($user1, $user2_each_connection)){
        return true;
    }else{
        return false;
    }
}

/** This function is used to prevent multiple submissions.
 * This function return true if there is no same submission or false if already submitted */
function multiple_submission($pin_content){
    if(isset($_SESSION['submission'])){
        if($_SESSION['submission'] === md5($pin_content)){
            return false;
        }else{
            $_SESSION['submission'] = md5($pin_content);
            return true;
        }
    }else{
        $_SESSION['submission'] = md5($pin_content);
        return true;
    }
}