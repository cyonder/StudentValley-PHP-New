<?php
ob_start();
require_once("./include/header.php");

//TODO: DON'T DISPLAY FROZEN USER IN CONNECTIONS AND CONNECTION REQUEST
/** When named a variable $user2, we refer to the user who is in interaction with user1 (logged in user). */
$user1_id = return_user_id($dbConnection, $_SESSION['email']);
$pagination = "none";
$no_request = NULL;
$no_connection = NULL;

/* --- Explanation of If Statements Below ---
 *
 * --- RequestTo ---
 * If 'RequestTo' is set. It means user1 clicked 'Connect' button. So, REQUEST_FROM is user1 and REQUEST_TO is user2.
 * Checking if the user passed his/her own id from 'RequestTo'. If did, display error message. Otherwise,
 * check if the request is already sent with function 'request_exist'. This function is necessary if someone tries
 * to submit a request multiple times, so database size will increase. After request sent, send user1 back to
 * user2's profile page. (This button is in user2's profile page).
 *
 * --- CancelRequestFrom ---
 * If 'CancelRequestFrom' is set. It means user1 clicked 'Cancel Request' button. So, REQUEST_FROM is user1
 * and REQUEST_TO is user2. Checking if the user passed his/her own id from 'CancelRequestFrom'. If did, display
 * error message. Otherwise, send user1 back to user2's profile page. (This button is user2's profile page).
 *
 * --- AcceptRequestOf ---
 * If 'AcceptRequestOf' is set. It means user1 clicked 'Accept Request' button. So, REQUEST_FROM is user2 and
 * REQUEST_TO is user1. Because connection request came from user2 to user1. Checking if the user passed his/her own id
 * from 'AcceptRequestOf'. If did, display error message.
 * This statement will do double work. When the users add each other, this statement will add both users to each others
 * connection list. If a user has no connection, it will add it but if a user already has a connection, it will concatenate the
 * added user with a comma. So, all the connection list will be separated with coma.
 * (This button is in user1's connections page).
 *
 * --- DeleteRequestOf ---
 * NOTE: 'DeleteRequestOf' is different than 'CancelRequestFrom'..!
 * If 'DeleteRequestOf' is set. It means user1 clicked 'Delete Request' button. So, REQUEST_FROM is user2 and
 * REQUEST_TO is user1. Because connection request came from user2 to user1. Checking if the user passed his/her own id
 * from 'DeleteRequestOf'. If did, display error message. Otherwise, send user1 back to connections page.
 * (This button is in user1's connections page).
 *
 * --- DisconnectFrom ---
 * If 'DisconnectFrom' is set. It means user1 clicked 'Disconnect' button. So, This statement will do double work.
 * When one of the users disconnect form other one, this statement will delete both users from each others connection list.
 * This statement's process is little bit confusing. You can read details from inside the statement.
 * --------------------------------------- */

if(isset($_GET['RequestTo'])){
    $user1 = $user1_id;
    $user2 = mysqli_real_escape_string($dbConnection, (int)$_GET['RequestTo']);
    $request_date = date("Y-m-d H:i:s");

    if($user1 == $user2){
        /* YOU CANNOT SEND CONNECTION REQUEST TO YOURSELF AND NEITHER CANCEL IT, ACCEPT IT, DELETE IT OR DISCONNECT IT */
    }
    else if(request_exist($dbConnection, $user1, $user2)){
        /* YOU ALREADY SENT A REQUEST TO THIS USER..! */
    }else{
        $query = "INSERT INTO REQUESTS ";
        $query .= "(REQUEST_FROM, REQUEST_TO, REQUEST_DATE) ";
        $query .= "VALUES('{$user1}', '{$user2}', '{$request_date}')";

        mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        header("location: /profile/".$user2);
    }
}
else if(isset($_GET['CancelRequestFrom'])){
    $user1 = $user1_id;
    $user2 = mysqli_real_escape_string($dbConnection, (int)$_GET['CancelRequestFrom']);

    if($user1 == $user2){
        /* YOU CANNOT SEND CONNECTION REQUEST TO YOURSELF AND NEITHER CANCEL IT, ACCEPT IT, DELETE IT OR DISCONNECT IT */
    }
    else if(!request_exist($dbConnection, $user1, $user2)){
        /* YOU HAVEN'T SEND ANY REQUEST TO THIS USER YET..! */
    }else{
        $query = "DELETE FROM REQUESTS ";
        $query .= "WHERE REQUEST_FROM = ";
        $query .= "'{$user1}' AND ";
        $query .= "REQUEST_TO = ";
        $query .= "'{$user2}'";

        mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        header("location: /profile/". $user2);
    }
}
else if(isset($_GET['AcceptRequestOf'])){
    $user1 = $user1_id;
    $user2 = mysqli_real_escape_string($dbConnection, (int)$_GET['AcceptRequestOf']);

    if($user1 == $user2){
        /* YOU CANNOT SEND CONNECTION REQUEST TO YOURSELF AND NEITHER CANCEL IT, ACCEPT IT, DELETE IT OR DISCONNECT IT */
    }
    else if(!request_exist($dbConnection, $user2, $user1)){
        /* YOU CAN'T CONNECT TO THIS USER WITHOUT SENDING A REQUEST..! */
    }else{
        $result1 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user1'");
        $data1 = mysqli_fetch_assoc($result1);
        $user1_connections = $data1['USER_CONNECTIONS']; //This is all the connections user1 has.
        $user1_each_connection = explode(",", $user1_connections); //This is every single connection user1 has. (In array).
        $user1_total_connection = count($user1_each_connection); //If user1 has no connections, explode will return 1..! (to avoid that!)

        if($user1_connections == NULL){
            $user1_total_connection = 0;
        }
        if($user1_total_connection == 0){
            mysqli_query($dbConnection, "UPDATE USERS SET USER_CONNECTIONS = '$user2' WHERE USER_ID = '$user1'");
        }else{
            mysqli_query($dbConnection, "UPDATE USERS SET USER_CONNECTIONS = CONCAT(USER_CONNECTIONS, ',$user2') WHERE USER_ID = '$user1'");
        }

        $result2 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user2'");
        $data2 = mysqli_fetch_assoc($result2);
        $user2_connections = $data2['USER_CONNECTIONS']; //This is all the connections user2 has.
        $user2_each_connection = explode(",", $user2_connections); //This is every single connection user2 has. (In array).
        $user2_total_connection = count($user2_each_connection);  //If user2 has no connections, explode will return 1..! (to avoid that!)

        if($user2_connections == NULL){
            $user2_total_connection = 0;
        }
        if($user2_total_connection == 0){
            mysqli_query($dbConnection, "UPDATE USERS SET USER_CONNECTIONS = '$user1' WHERE USER_ID = '$user2'");
        }else{
            mysqli_query($dbConnection, "UPDATE USERS SET USER_CONNECTIONS = CONCAT(USER_CONNECTIONS, ',$user1') WHERE USER_ID = '$user2'");
        }

        mysqli_query($dbConnection, "DELETE FROM REQUESTS WHERE REQUEST_FROM = '$user2' AND REQUEST_TO = '$user1'"); //Delete the related connection request from REQUEST table.
        header("location: /connections");
    }
}
else if(isset($_GET['DeleteRequestOf'])){
    $user1 = $user1_id;
    $user2 = mysqli_real_escape_string($dbConnection, (int)$_GET['DeleteRequestOf']);

    if($user1 == $user2){
        /* YOU CANNOT SEND CONNECTION REQUEST TO YOURSELF AND NEITHER CANCEL IT, ACCEPT IT, DELETE IT OR DISCONNECT IT */
    }
    else if(!request_exist($dbConnection, $user2, $user1)){
        /* This user haven't send you any request..! */
    }else{
        $query = "DELETE FROM REQUESTS ";
        $query .= "WHERE REQUEST_FROM = ";
        $query .= "'{$user2}' AND ";
        $query .= "REQUEST_TO = ";
        $query .= "'{$user1}'";

        mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        header("location: /connections");
    }
}else if(isset($_GET['DisconnectFrom'])){
    $user1 = $user1_id;
    $user2 = mysqli_real_escape_string($dbConnection, (int)$_GET['DisconnectFrom']);

    if($user1 == $user2){
        /* YOU CANNOT SEND CONNECTION REQUEST TO YOURSELF AND NEITHER CANCEL IT, ACCEPT IT, DELETE IT OR DISCONNECT IT */
   }else{
        echo "
        <div id='dialogbox-overlay'></div>
        <div id='dialogbox'>
            <div>
                <div id='dialogbox-head'></div>
                <div id='dialogbox-body'></div>
                <div id='dialogbox-foot'></div>
            </div>
        </div>
        <script src='/js/dialogbox.js'></script>
        <script>Confirm.render('Are you sure you want to disconnect your friend?', 'Your friend will be removed from your connections.<br /><br />Do you want to continue?', 'Disconnect', '$user2')</script>
        ";
    }
}

/* Paging connection requests */
$page = isset($_GET['Page']) ? (int)$_GET['Page'] : 1; //If page is not set, set the default 1.
$per_page = isset($_GET['PerPage']) && $_GET['PerPage'] <= 50 ? (int)$_GET['PerPage'] : 5;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0; //Every page, it will display the next 5.

/* Return the information of people who sent request to user1 */
$query = "SELECT SQL_CALC_FOUND_ROWS u.* FROM USERS u , REQUESTS r WHERE (r.REQUEST_TO = '{$user1_id}')
          and (u.USER_ID = r.REQUEST_FROM) ORDER BY r.REQUEST_DATE DESC LIMIT {$start}, {$per_page};";
$result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

$first_name = array();
$last_name = array();
$school = array();
$program = array();
$picture = array();

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $user_id[] = $row['USER_ID'];
        $first_name[] = $row['USER_FIRST_NAME'];
        $last_name[] = $row['USER_LAST_NAME'];
        $school[] = $row['USER_SCHOOL'];
        $program[] = $row['USER_PROGRAM'];
        $picture[] = $row['USER_PICTURE_ID'];
    }
}else{
    $no_request = "<div class='font-14' style='text-align: center'>You don't have any connection request.</div>";
}

/* Paging links for connection requests */
$query = "SELECT FOUND_ROWS() as TOTAL";
$result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
while($data = mysqli_fetch_assoc($result)){ $total = $data['TOTAL']; }
$pages = ceil($total / $per_page); //Ceil rounds up.

if($total > 5 && $pages > 1){
    $pagination = "display: block;";
}else{
    $pagination = "display: none;";
    $request_style = "conn-request-inline"; /* If user have less than 4 request, remove margin-bottom (activating class) */
}

/* Return user1's connections and information of them */
$result1 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user1_id'");
$data1 = mysqli_fetch_assoc($result1);
$user1_connections = $data1['USER_CONNECTIONS']; //This is all the connections user1 has.
$user1_each_connection = explode(",", $user1_connections); //This is every single connection user1 has. (In array).
$user1_total_connection = count($user1_each_connection);

if($user1_connections == NULL){ //If user1 has no connections, explode will return 1..! (to avoid that!)
    $user1_total_connection = 0; //User don't have any friends.
    $no_connection = "<div class='font-14' style='text-align: center'>You have no connection. You can search your friends in the search bar above.</div>";
}else{
    $conn_first_name = array();
    $conn_last_name = array();
    $conn_school = array();
    $conn_program = array();
    $conn_picture = array();

    if(isset($_GET['Letter'])){
        $letter = mysqli_real_escape_string($dbConnection, $_GET['Letter']);
        $user1_total_connection = 0;

        foreach($user1_each_connection as $value){
            $query = "SELECT * FROM USERS ";
            $query .= "WHERE USER_ID = '{$value}' ";
            $query .= "AND USER_FIRST_NAME LIKE '$letter%'";

            $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
            $row = mysqli_fetch_assoc($result);

            if(mysqli_num_rows($result) > 0){
                $user1_total_connection++;
                $counter++;

                $conn_user_id[] = $row['USER_ID'];
                $conn_first_name[] = $row['USER_FIRST_NAME'];
                $conn_last_name[] = $row['USER_LAST_NAME'];
                $conn_school[] = $row['USER_SCHOOL'];
                $conn_program[] = $row['USER_PROGRAM'];
                $conn_picture[] = $row['USER_PICTURE_ID'];
            }
        }
        if($counter == 0){
            $no_connection = "<div class='font-14' style='text-align: center'>You have no connection with the letter '" . strtoupper($letter) . "'. <a href='/connections'>Show all connections</a></div>";
        }
    }else{
        foreach($user1_each_connection as $value){
            $query = "SELECT * FROM USERS WHERE USER_ID = '{$value}'";

            $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
            $row = mysqli_fetch_assoc($result);

            if(mysqli_num_rows($result) > 0){

                $conn_user_id[] = $row['USER_ID'];
                $conn_first_name[] = $row['USER_FIRST_NAME'];
                $conn_last_name[] = $row['USER_LAST_NAME'];
                $conn_school[] = $row['USER_SCHOOL'];
                $conn_program[] = $row['USER_PROGRAM'];
                $conn_picture[] = $row['USER_PICTURE_ID'];
            }
        }
    }
}
?>
<div id="container" class="clearfix">
    <?php require_once("./include/sidebar.php"); ?>
    <div id="content">
        <div class="title">CONNECTION REQUESTS</div>
        <div class="content-box">
            <?php for($i = 0; $i < count($user_id); $i++){ ?>
                <div class="conn-request <?php echo $request_style; ?>">
                    <table width="100%">
                        <tr>
                            <td width="63" rowspan="3">
                                <a href="/profile/<?php echo $user_id[$i]; ?>">
                                    <img src="/image/profile/<?php echo $picture[$i] = ($picture[$i] != NULL ? $picture[$i] : "Default.png"); ?>" >
                                </a>
                            </td>
                            <td width="487" valign="top"><a href="/profile/<?php echo $user_id[$i]; ?>"><?php echo $first_name[$i] . " " . $last_name[$i]; ?></a></td>
                            <td width="101" align="right" class="button-3" rowspan="3">
                                <a href="/connections?AcceptRequestOf=<?php echo $user_id[$i]; ?>" style="background: -moz-linear-gradient(180deg,#6BB72C, #51891E);
                                    background: -ms-linear-gradient(180deg,#6BB72C, #51891E);
                                    background: -o-linear-gradient(180deg,#6BB72C, #51891E);
                                    background: linear-gradient(180deg,#6BB72C, #51891E);">Accept Request</a>
                            </td>
                            <td width="101" align="right" class="button-3" rowspan="3">
                                <a href="/connections?DeleteRequestOf=<?php echo $user_id[$i]; ?>" style="background: -webkit-linear-gradient(180deg,#D63301, #B71C1F);
                                    background: -moz-linear-gradient(180deg,#D63301, #B71C1F);
                                    background: -ms-linear-gradient(180deg,#D63301, #B71C1F);
                                    background: -o-linear-gradient(180deg,#D63301, #B71C1F);
                                    background: linear-gradient(180deg,#D63301, #B71C1F);">Delete Request</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-14" width="487" valign="middle" title="<?php echo $school[$i]; ?>"><?php echo $school[$i]; ?></td>
                        </tr>
                        <tr>
                            <td class="font-14" width="487" valign="bottom" title="<?php echo $program[$i]; ?>"><?php echo $program[$i]; ?></td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
            <?php echo $no_request; ?>
            <div class="pagination" style="<?php echo $pagination; ?>">
                <?php for($i = 1; $i <= $pages; $i++){ ?>
                    <a href="?Page=<?php echo $i; ?>&PerPage=<?php echo $per_page; ?>"<?php if($page == $i){echo " class='conn-selected'";} ?>><?php echo $i; ?></a>
                <?php } ?>
            </div>
        </div>
        <div class="title">YOUR CONNECTIONS
            <div id="conn-find">
                <a href="/connections?Letter=a" class="conn-find-item">A</a>
                <a href="/connections?Letter=b" class="conn-find-item">B</a>
                <a href="/connections?Letter=c" class="conn-find-item">C</a>
                <a href="/connections?Letter=d" class="conn-find-item">D</a>
                <a href="/connections?Letter=e" class="conn-find-item">E</a>
                <a href="/connections?Letter=f" class="conn-find-item">F</a>
                <a href="/connections?Letter=g" class="conn-find-item">G</a>
                <a href="/connections?Letter=h" class="conn-find-item">H</a>
                <a href="/connections?Letter=i" class="conn-find-item">I</a>
                <a href="/connections?Letter=j" class="conn-find-item">J</a>
                <a href="/connections?Letter=k" class="conn-find-item">K</a>
                <a href="/connections?Letter=l" class="conn-find-item">L</a>
                <a href="/connections?Letter=m" class="conn-find-item">M</a>
                <a href="/connections?Letter=n" class="conn-find-item">N</a>
                <a href="/connections?Letter=o" class="conn-find-item">O</a>
                <a href="/connections?Letter=p" class="conn-find-item">P</a>
                <a href="/connections?Letter=q" class="conn-find-item">Q</a>
                <a href="/connections?Letter=r" class="conn-find-item">R</a>
                <a href="/connections?Letter=s" class="conn-find-item">S</a>
                <a href="/connections?Letter=t" class="conn-find-item">T</a>
                <a href="/connections?Letter=u" class="conn-find-item">U</a>
                <a href="/connections?Letter=v" class="conn-find-item">V</a>
                <a href="/connections?Letter=w" class="conn-find-item">W</a>
                <a href="/connections?Letter=x" class="conn-find-item">X</a>
                <a href="/connections?Letter=y" class="conn-find-item">Y</a>
                <a href="/connections?Letter=z" class="conn-find-item">Z</a>
            </div>
        </div>
        <div class="content-box clearfix">
            <?php for($i = 0; $i < $user1_total_connection; $i++){?>
                <div class="conn-yours">
                    <table width="362px">
                        <tr>
                            <td width="63" rowspan="3">
                                <a href="/profile/<?php echo $conn_user_id[$i]; ?>">
                                    <img src="/image/profile/<?php echo $conn_picture[$i] = ($conn_picture[$i] != NULL ? $conn_picture[$i] : "Default.png"); ?>" >
                                </a>
                            </td>
                            <td valign="top"><a href="/profile/<?php echo $conn_user_id[$i]; ?>"><?php echo $conn_first_name[$i] . " " . $conn_last_name[$i]; ?></a></td>
                            <td valign="middle" class="button-3" width="20" align="right">
                                <a href="/connections?DisconnectFrom=<?php echo $conn_user_id[$i]; ?>" title="Disconnect"
                                   style="background: -webkit-linear-gradient(180deg,#D63301, #B71C1F);
                                background: -moz-linear-gradient(180deg,#D63301, #B71C1F);
                                background: -ms-linear-gradient(180deg,#D63301, #B71C1F);
                                background: -o-linear-gradient(180deg,#D63301, #B71C1F);
                                background: linear-gradient(180deg,#D63301, #B71C1F);">x</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-14" valign="middle" title="<?php echo $conn_school[$i]; ?>"><?php echo $conn_school[$i]; ?></td>
                        </tr>
                        <tr>
                            <td class="font-14" valign="bottom" title="<?php echo $conn_program[$i]; ?>"><?php echo $conn_program[$i]; ?></td>
                        <tr class="button-3">
                            <td colspan="2" style="padding: 7px 0 2px 0"><a href="/messages?SendTo=<?php echo $conn_user_id[$i]; ?>">Send Message</a></td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
            <?php echo $no_connection; ?>
        </div>
    </div>
</div>
<?php
require_once("./include/footer.php");
?>