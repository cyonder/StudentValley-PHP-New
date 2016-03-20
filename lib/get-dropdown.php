<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/lib/config.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-database.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-common.php");

$dbConnection = open_connection();
mysqli_set_charset($dbConnection, 'utf8mb4');

$user1_id = return_user_id($dbConnection, $_SESSION['email']);
if(isset($_GET['School'])){ $school_name = mysqli_real_escape_string($dbConnection, $_GET['School']); }

echo "<option value='Select Your Program' disabled selected>Select Your Program</option>";

/* Get the program names from database
 * Get user 1's program name
 * Start outputting program names from database into drop down
 * If any of the program names and user's school matches with user 1's program and school, mark it as selected
 * Checking school beside program is important! If many schools has same program, if statement will mark other school's
 * program as selected. If we don't check school!
 * Now, either user 1 sees his/her program as selected or sees 'Select Your Program' because
 * he/she haven't been chosen any program from the list yet.
 * */

$program_names = array();

/* GET PROGRAM LIST */
$query = "SELECT PROGRAM_NAME FROM PROGRAMS WHERE PROGRAM_SCHOOL = '$school_name' ORDER BY PROGRAM_NAME ASC";
$result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

while($data = mysqli_fetch_assoc($result)){ $program_names[] = $data['PROGRAM_NAME']; }

/* GET USER 1'S PROGRAM NAME */
$query = "SELECT USER_SCHOOL, USER_PROGRAM FROM USERS WHERE USER_ID = '$user1_id'";
$result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

while($data = mysqli_fetch_assoc($result)){
    $user_school = $data['USER_SCHOOL'];
    $user_program = $data['USER_PROGRAM'];
}

foreach($program_names as $program_name){
    if(($user_program == $program_name) && ($user_school == $school_name)){
        echo "<option value='$program_name' selected>$program_name</option>";
    }else{
        echo "<option value='$program_name'>$program_name</option>";
    }
}

close_connection($dbConnection);