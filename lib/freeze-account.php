<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/lib/config.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-database.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-common.php");

$dbConnection = open_connection();

$user1_id = return_user_id($dbConnection, $_SESSION['email']);
$id = mysqli_real_escape_string($dbConnection, $_POST['id']);

if($user1_id == $id){ //Check if user is freezing his/her own account.
    $query = "UPDATE SETTINGS SET SETTING_FREEZE = 'Yes' WHERE SETTING_USER = '$id'";
    mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

    session_destroy();
}
close_connection($dbConnection);
