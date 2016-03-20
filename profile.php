<?php
ob_start();
require_once("./include/header.php");

$user1_id = return_user_id($dbConnection, $_SESSION['email']);

if(!isset($_GET['id'])){header("Location: /profile/" . $user1_id);}
    /** Need this variable ( $user2_id ) where we need to refer to the user who is in
     * interaction with user1 (logged in user).
     */
    $user2_id = mysqli_real_escape_string($dbConnection, (int)$_GET['id']);

    $request_from_user1 = request_exist($dbConnection, $user1_id, $user2_id);
    $request_from_user2 = request_exist($dbConnection, $user2_id, $user1_id);

    /* --- Explanation of If Statements Below ---
     * If there is a connection request from user2:
     * Change button text to 'Accept Request', change link of the button to 'AcceptRequestOf' and change button color to green.
     *
     * If there is a connection request from user1:
     * Change button text to 'Cancel Request', change link of the button to 'CancelRequestFrom' and change button color to orange.
     *
     * If user1 and user2 are connected:
     * Change button text to 'Disconnect', change link of the button to 'DisconnectFrom' and change button color to red.
     *
     * If none of the if statements work:
     * Change button text to 'Connect' and change the link of the button to 'RequestTo' and change button color to blue.
     * --------------------------------------- */
    if($request_from_user2){
        $button_text = "Accept Request";
        $link = "/connections?AcceptRequestOf=";
        $style = "
        background: -webkit-linear-gradient(180deg,#6BB72C, #51891E);
        background: -moz-linear-gradient(180deg,#6BB72C, #51891E);
        background: -ms-linear-gradient(180deg,#6BB72C, #51891E);
        background: -o-linear-gradient(180deg,#6BB72C, #51891E);
        background: linear-gradient(180deg,#6BB72C, #51891E);
        ";
    }else if($request_from_user1){
        $button_text = "Cancel Request";
        $link = "/connections?CancelRequestFrom=";
        $style = "
        background: -webkit-linear-gradient(180deg,#FDB813, #D99413);
        background: -moz-linear-gradient(180deg,#FDB813, #D99413);
        background: -ms-linear-gradient(180deg,#FDB813, #D99413);
        background: -o-linear-gradient(180deg,#FDB813, #D99413);
        background: linear-gradient(180deg,#FDB813, #D99413);
        ";
    }else if(are_they_connected($dbConnection, $user1_id, $user2_id)){
        $button_text = "Disconnect";
        $link = "/connections?DisconnectFrom=";
        $style = "
        background: -webkit-linear-gradient(180deg,#D63301, #B71C1F);
        background: -moz-linear-gradient(180deg,#D63301, #B71C1F);
        background: -ms-linear-gradient(180deg,#D63301, #B71C1F);
        background: -o-linear-gradient(180deg,#D63301, #B71C1F);
        background: linear-gradient(180deg,#D63301, #B71C1F);
        ";
    }else{
        $button_text = "Connect";
        $link = "/connections?RequestTo=";
        $style = "
        background: -webkit-linear-gradient(180deg,#307FB7, #084F81);
        background: -moz-linear-gradient(180deg,#307FB7, #084F81);
        background: -ms-linear-gradient(180deg,#307FB7, #084F81);
        background: -o-linear-gradient(180deg,#307FB7, #084F81);
        background: linear-gradient(180deg,#307FB7, #084F81);
        ";
    }

    /* Checking if the user is in his/her page or not.
     * According to that, this statement will decide to display buttons or not.
     * Because, we don't wanna display Connect Buttons to the user1 when he/she is in his/her own profile page.
     */
    $buttons = ($user1_id == $user2_id ? "none" : "block");

    /* The query below returns all the necessary information for profile page. */
    $query = "SELECT * FROM USERS WHERE USER_ID = '$user2_id'";
    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

    /* The query below returns account's freeze setting */
    $query2 = "SELECT SETTING_FREEZE FROM SETTINGS WHERE SETTING_USER = '$user2_id'";
    $result2 = mysqli_query($dbConnection, $query2) or die(mysqli_error($dbConnection));

    while($data = mysqli_fetch_assoc($result2)){ $frozen = $data['SETTING_FREEZE']; }

    if(mysqli_num_rows($result) == 1 && $frozen == 'No'){
        while($row = mysqli_fetch_assoc($result)){
            $first_name = $row['USER_FIRST_NAME'];
            $last_name = $row['USER_LAST_NAME'];
            $email = $row['USER_EMAIL'];
            $school = $row['USER_SCHOOL'];
            $program = $row['USER_PROGRAM'];
            $registration_date = date_create($row['USER_REGISTRATION_DATE']);
            $website = $row['USER_WEBSITE'];
            $picture = $row['USER_PICTURE_ID'];
        }

        /* Return user2's connections and information of them */
        $result1 = mysqli_query($dbConnection, "SELECT USER_CONNECTIONS FROM USERS WHERE USER_ID = '$user2_id'");
        $data1 = mysqli_fetch_assoc($result1);
        $user1_connections = $data1['USER_CONNECTIONS']; //This is all the connections user1 has.
        $user1_each_connection = explode(",", $user1_connections); //This is every single connection user1 has. (In array).
        $user1_total_connection = count($user1_each_connection);

        if($user1_connections == NULL){ //If user1 has no connections, explode will return 1..! (to avoid that!)
            $user1_total_connection = 0; //User don't have any friends.
            $no_connection = "<div class='conn-font' style='text-align: center'>You have no connection. You can search your friends in the search bar above.</div>";
        }else{
            $conn_first_name = array();
            $conn_last_name = array();
            $conn_school = array();
            $conn_program = array();
            $conn_picture = array();

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
        ?>

        <div id="container" class="clearfix">
            <?php require_once("./include/sidebar.php"); ?>
            <div id="content">
                <div id="top" class="clearfix">
                    <div id="profile-name">
                        <?php echo $first_name . " " . $last_name; ?>
                    </div>
                    <div id="profile-buttons" class="button-3"  style="display: <?php echo $buttons; ?>">
                        <ul>
                            <li><a href="<?php echo $link . $user2_id ?>" style="<?php echo $style ?>"><?php echo $button_text ?></a></li>
                            <li><a href="/messages?SendTo=<?php echo $user2_id ?>">Send Message</a></li>
                        </ul>
                    </div>
                </div>
                <div id="profile-left">
                    <a href="/image/profile/<?php echo $picture = ($picture != NULL ? $picture : "Default.png"); ?>" data-lightbox="image">
                        <img src="/image/profile/<?php echo $picture = ($picture != NULL ? $picture : "Default.png"); ?>" >
                    </a>
                    <div class="title">INFORMATION</div>
                    <div class="content-box">
                        <div class="profile-item school-logo" style="display: <?php echo $display = ($school != NULL ? "block" : "none"); ?>" title="<?php echo $school ?>"><?php echo $school ?></div>
                        <div class="profile-item program-logo" style="display: <?php echo $display = ($program != NULL ? "block" : "none"); ?>" title="<?php echo $program ?>"><?php echo $program ?></div>
                        <div class="profile-item email-logo" style="display: <?php echo $display = ($email != NULL ? "block" : "none"); ?>" title="<?php echo $email ?>"><?php echo $email ?></div>
                        <div class="profile-item website-logo" style="display: <?php echo $display = ($website != NULL ? "block" : "none"); ?>"><a href="http://<?php echo $website; ?>" target="_blank" title="<?php echo $website ?>"><?php echo $website ?></a></div>
                        <div class="profile-item member-logo" style="display: <?php echo $display = ($registration_date != NULL ? "block" : "none"); ?>"><?php echo  date_format($registration_date,"F j, Y") ?></div>
                    </div>
                </div>
                <div id="profile-right">
                    <div class="title">COURSES</div>
                    <div class="content-box">
                        <div class="profile-item courses-logo"><a href="#">OOP345</a></div>
                        <div class="profile-item courses-logo"><a href="#">DBS301</a></div>
                        <div class="profile-item courses-logo"><a href="#">CUL710</a></div>
                        <div class="profile-item courses-logo"><a href="#">INT322</a></div>
                    </div>
                    <div class="title">GROUPS</div>
                    <div class="content-box">
                        <div class="profile-item groups-logo"><a href="#" title="Android Open Source Club">Android Open Source Club</a></div>
                        <div class="profile-item groups-logo"><a href="#" title="Seneca Tutors">Seneca Tutors</a></div>
                        <div class="profile-item groups-logo"><a href="#" title="International Canada Robot Project">International Canada Robot Project</a></div>
                        <div class="profile-item groups-logo"><a href="#" title="DBS Group Project">DBS Group Project</a></div>
                        <div class="profile-item groups-logo"><a href="#" title="CPA Seneca Student">CPA Seneca Student</a></div>
                    </div>
                    <div class="title">CONNECTIONS</div>
                    <div class="content-box clearfix">
                        <?php for($i = 0; $i < $user1_total_connection; $i++){?>
                            <div class="profile-conn">
                                <a href="/profile/<?php echo $conn_user_id[$i]; ?>" title="<?php echo $conn_first_name[$i] . " " . $conn_last_name[$i]; ?>">
                                    <img src="/image/profile/<?php echo $conn_picture[$i] = ($conn_picture[$i] != NULL ? $conn_picture[$i] : "Default.png"); ?>" >
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
}else{
    /* Defined min-height to bring the footer close to error */
        echo "<div id='container' style='min-height: 80px;'>";
        echo "<div class='error'>";
            echo "<div class='error-wrapper'>";
                echo "<p class='message-title'>Profile Unavailable</p>
                      <p>Sorry, this profile is not available at the moment. Either this account is frozen by its user or
                      never been created.
                      <a href='javascript:history.back()'><b>Click here</b></a> to go back to last page you viewed.</p>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
}
require_once("./include/footer.php");
?>
