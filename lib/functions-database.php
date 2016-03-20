<?php
/**
 * When this file included to a page, config.php file should be included to that file, as well.
 * These database connection functions need some values from config.php file to work.
 */

/** Open connection to the database */
function open_connection(){
    $dbConnection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die("Unable to connect to MySQL");
    return $dbConnection;
}

/** Close connection to the database */
function close_connection($dbConnection){
    mysqli_close($dbConnection);
}