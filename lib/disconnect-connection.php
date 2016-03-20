<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/lib/config.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-database.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-common.php");

$dbConnection = open_connection();

$user1 = return_user_id($dbConnection, $_SESSION['email']);
if($user1 != NULL){ //User only can delete from his/her own connection list
    $user2 = mysqli_real_escape_string($dbConnection, $_POST['id']);

    $result1 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user1'");
    $data1 = mysqli_fetch_assoc($result1);
    $user1_connections = $data1['USER_CONNECTIONS']; //This is all the connections user1 has.
    $user1_each_connection = explode(",", $user1_connections); //This is every single connection user1 has. (In array).

    $key1 = array_search($user2, $user1_each_connection); //Find which key is user2 in the array,
    unset($user1_each_connection[$key1]); //Remove the user2 from user1's connection list.
    $user1_new_total_connection = count($user1_each_connection); //Calculate the total number of user1's connections after deletion.
    $user1_each_connection = array_values($user1_each_connection); //Reorder arrays.
    for($i = 0; $i < $user1_new_total_connection; $i++){ //Create a string with comas for the new connection list.
        if($i == $user1_new_total_connection - 1){ //Check if it is adding the last connection to the list and don't add come at the end.
            $user1_new_connections .= $user1_each_connection[$i];
        }else{
            $user1_new_connections .= $user1_each_connection[$i] . ",";
        }
    }
    mysqli_query($dbConnection, "UPDATE USERS SET USER_CONNECTIONS = '$user1_new_connections' WHERE USER_ID = '$user1'");

    $result2 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user2'");
    $data2 = mysqli_fetch_assoc($result2);
    $user2_connections = $data2['USER_CONNECTIONS']; //This is all the connections user2 has.
    $user2_each_connection = explode(",", $user2_connections); //This is every single connection user2 has. (In array).

    $key2 = array_search($user1, $user2_each_connection); //Find which key is user1 in the array,
    unset($user2_each_connection[$key2]); //Remove the user1 from user2's connection list.
    $user2_each_connection = array_values($user2_each_connection); //Reorder arrays.
    $user2_new_total_connection = count($user2_each_connection); //Calculate the total number of user2's connections after deletion.

    for($i = 0; $i < $user2_new_total_connection; $i++){ //Create a string with comas for the new connection list.
        if($i == $user2_new_total_connection - 1){ //Check if it is adding the last connection to the list and don't add come at the end.
            $user2_new_connections .= $user2_each_connection[$i];
        }else{
            $user2_new_connections .= $user2_each_connection[$i] . ",";
        }
    }
    mysqli_query($dbConnection, "UPDATE USERS SET USER_CONNECTIONS = '$user2_new_connections' WHERE USER_ID = '$user2'");
}
close_connection($dbConnection);