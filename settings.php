<?php
ob_start();
require_once("./include/header.php");

$error_message = "none";
$error_message_2 = "none";

if(isset($_GET['Success'])){
    $success_message = "block";
}else{
    $success_message = "none";
}

$pass = 0;
$user1_id = return_user_id($dbConnection, $_SESSION['email']);

$query = "SELECT * FROM USERS WHERE USER_ID = '$user1_id'";

$result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
while($row = mysqli_fetch_assoc($result)){
    $first_name = $row['USER_FIRST_NAME'];
    $last_name = $row['USER_LAST_NAME'];
    $email = $row['USER_EMAIL'];
    $password = $row['USER_PASSWORD'];
    $display_name = $row['USER_DISPLAY_NAME'];
    $school = $row['USER_SCHOOL'];
    $program = $row['USER_PROGRAM'];
    $website = $row['USER_WEBSITE'];
    $picture = $row['USER_PICTURE_ID'];
}

if($_POST){
    if($_GET["Change"] == "Picture"){
        if($_FILES['picture']['error'] != UPLOAD_ERR_NO_FILE || $_POST['remove']){ //Check if user selected any file to upload
            if(isset($_FILES['picture']) && !$_POST['remove']){
                if($_FILES['picture']['type'] == "image/jpeg" || $_FILES['picture']['type'] == "image/png"){
                    if($_FILES['picture']['size'] < 1048576){
                        unlink("image/profile/" . $picture); //Delete old picture
                        $picture_id = md5(uniqid(date("Y-m-d H:i:s"), true) * rand()) . '.png'; //Create unique id for new picture
                        move_uploaded_file($_FILES['picture']['tmp_name'], "image/profile/" . $picture_id); //Upload new picture

                        $query = "UPDATE USERS SET USER_PICTURE_ID = '$picture_id' WHERE USER_ID = $user1_id";
                        mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
                        header("location: ".$_SERVER['PHP_SELF']."?Success=PictureUpload");
                    }else{
                        $picture_error = "Maximum picture size is 1MB";
                        $error_message = "block";
                    }
                }else{
                    $picture_error = "Acceptable file extensions are JPEG and PNG";
                    $error_message = "block";
                }
            }else{ //If user clicked remove
                if($picture == NULL){
                    $picture_error = "You don't have a profile picture to delete";
                    $error_message = "block";
                }else{
                    unlink("image/profile/" . $picture); //Delete picture

                    $query = "UPDATE USERS SET USER_PICTURE_ID = NULL WHERE USER_ID = $user1_id"; //Change picture id to NULL
                    mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

                    header("location: ".$_SERVER['PHP_SELF']."?Success=PictureRemove");
                }
            }
        }else{
            $error = "You haven't choose any picture to upload";
            $error_message = "block";
        }
    }

    if($_GET["Change"] == "Info"){
        /* Checking if user have changed some information
         * if did, update user information. If not,
         * display error message. */
        if($first_name == $_POST['first_name'] &&
            $last_name == $_POST['last_name'] &&
            $display_name == $_POST['display_name'] &&
            $school == $_POST['school'] &&
            $program == $_POST['program'] &&
            $website == $_POST['website']){

            $error = "You haven't make any changes in your information";
            $pass = -1; //When this error triggered, we change pass to minus so, the form will not be submitted even though inputs are correct.
        }

        /* FIRST NAME */
        if(!empty($_POST['first_name'])){
            if(preg_match("/^[a-zA-Z]+$/", $_POST['first_name'])){
                $user_first_name = trim(mysqli_real_escape_string($dbConnection, $_POST['first_name']));
                $pass++;
            }else{
                $first_name_error = "First name can contain only letters";
            }
        }else{
            $first_name_error = "Please enter your first name";
        }

        /* LAST NAME */
        if(!empty($_POST['last_name'])){
            if(preg_match("/^[a-zA-Z]+$/", $_POST['last_name'])){
                $user_last_name = trim(mysqli_real_escape_string($dbConnection, $_POST['last_name']));
                $pass++;
            }else{
                $last_name_error = "Last name can contain only letters";
            }
        }else{
            $last_name_error = "Please enter your last name";
        }

        /* DISPLAY NAME */

        /* Checking if the display name field is empty
         * if it is not empty, check if there is any unwanted characters
         * if display name is validated, check if the user name is already exist
         * if not exist save it into database.
         * If the display field is empty
         * check if the display name is null (Checking if user ever saved display name)
         * if null don't show error, but if user ever saved name
         * don't let the user delete his/her display name.
         *
         * So, after you saved your display name into database there is no way to leave it empty
         * you can only change it. */

        if(!empty($_POST['display_name'])){
            if(preg_match("/^[a-zA-Z0-9]+$/", $_POST['display_name'])){ //Checking if the display name has unwanted characters
                if(strlen($_POST['display_name']) >= 5){ //Checking if the display name hast at least 5 characters
                    $user_display_name_temp = trim(mysqli_real_escape_string($dbConnection, $_POST['display_name']));

                    $query = "SELECT USER_DISPLAY_NAME FROM USERS WHERE USER_DISPLAY_NAME = '$user_display_name_temp'";
                    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
                    if(mysqli_num_rows($result) == 0){ //Checking if display name exist
                        $user_display_name = $user_display_name_temp;
                        $pass++;
                    }else{
                        $query = "SELECT USER_DISPLAY_NAME FROM USERS WHERE USER_ID = '$user1_id'";
                        $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

                        while($data = mysqli_fetch_assoc($result)){ $user_display_name = $data['USER_DISPLAY_NAME']; }

                        if($user_display_name == $user_display_name_temp){ //If user saves his,her own display name, don't show error
                            $pass++;
                        }else{
                            $display_name_error = "Sorry, the display name you tried already exist";
                        }
                    }
                }else{
                    $display_name_error = "Display name should be at least 5 characters long";
                }
            }else{
                $display_name_error = "Display name can contain letters and numbers only";
            }
        }else{
            $query = "SELECT USER_DISPLAY_NAME FROM USERS WHERE USER_ID = '$user1_id'";
            $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

            while($data = mysqli_fetch_assoc($result)){ $user_display_name = $data['USER_DISPLAY_NAME']; }

            if($user_display_name == NULL){ //Checking if display name is NULL
                $user_display_name = NULL;
                $pass++;
            }else{
                $display_name_error = "Sorry, the display name cannot be empty";
            }
        }

        /* SCHOOL */
        $query = "SELECT USER_SCHOOL FROM USERS WHERE USER_ID = $user1_id";
        $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        while($data = mysqli_fetch_assoc($result)){ $user_school_db = $data['USER_SCHOOL']; }

        if(isset($_POST['school']) && $_POST['school'] != "Select Your School"){
            $user_school = mysqli_real_escape_string($dbConnection, $_POST['school']);
            $pass++;
        }else{
            if($user_school_db == NULL){
                $user_school = mysqli_real_escape_string($dbConnection, $_POST['school']);
                $pass++;
            }else{
                $user_school_error =  "Please, choose your school";
            }
        }

        /* PROGRAM */
        $query = "SELECT USER_PROGRAM FROM USERS WHERE USER_ID = $user1_id";
        $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

        while($data = mysqli_fetch_assoc($result)){ $user_program_db = $data['USER_PROGRAM']; }

        if(isset($_POST['program']) && $_POST['program'] != "Select Your Program"){
            $user_program = mysqli_real_escape_string($dbConnection, $_POST['program']);
            $pass++;
        }else{
            if($user_program_db == NULL){ //If user haven't been changed, don't display error.
                $user_program = mysqli_real_escape_string($dbConnection, $_POST['program']);
                $pass++;
            }else{
                $user_program_error =  "Please, choose your program";
            }
        }

        /* WEBSITE */
        if(!empty($_POST['website'])){
            if(preg_match("/^(?:(ftp|http|https):\/\/)?(?:[\w-]+\.)+[a-z]{3,6}$/", $_POST['website'])){
                $user_website = mysqli_real_escape_string($dbConnection, $_POST['website']);
                $pass++;
            }else{
                $website_error = "Invalid website address";
            }
        }else{
            $user_website = NULL;
            $pass++;
        }

        if($pass == 6){ //6 pass means form is validated
            $query = "UPDATE USERS SET ";
            $query .= "USER_FIRST_NAME = '$user_first_name', ";
            $query .= "USER_LAST_NAME = '$user_last_name', ";
            $query .= "USER_DISPLAY_NAME = '$user_display_name', ";
            $query .= "USER_SCHOOL = '$user_school', ";
            $query .= "USER_PROGRAM = '$user_program', ";
            $query .= "USER_WEBSITE = '$user_website' ";
            $query .= "WHERE USER_ID = '$user1_id'";

            mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
            header("location: ".$_SERVER['PHP_SELF']."?Success=Info");
        }else{
            $error_message = "block";
        }
    }

    if($_GET["Change"] == "Email"){
        /* CONFIRMATION PASSWORD */
        if(!empty($_POST['password'])){
            if(password_verify($_POST['password'], $password)){
                $confirmation_password = mysqli_real_escape_string($dbConnection, $_POST['password']);
                $pass++;
            }else{
                $confirmation_error = "Current password is not correct";
            }
        }else{
            $confirmation_error = "Please enter your current password for confirmation to change your email";
        }

        /* EMAIL */
        if(!empty($_POST['email'])){
            if($email != $_POST['email']){
                $query = "SELECT SETTING_EMAIL_TIME FROM SETTINGS WHERE SETTING_USER = $user1_id";
                $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
                while($data = mysqli_fetch_assoc($result)){ $email_last_change_time = $data['SETTING_EMAIL_TIME']; }

                if($email_last_change_time != NULL){
                    $email_last_change_time = new DateTime($email_last_change_time);
                    $time_now = new DateTime('now');
                    $email_time_difference = $time_now->diff($email_last_change_time);

                    $email_change_available_time = $email_last_change_time;
                    $email_change_available_time->add(new DateInterval('P2D')); //Add 2 days to last change time to find available change time
                }else{
                    $email_last_change_time = NULL;
                }

                if($email_time_difference->d >= 2 || $email_last_change_time == NULL){ //Check if user is trying to change email again before 2 days passes.
                    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                        if(!user_exist($dbConnection, $_POST['email'])){
                            $user_email = trim(mysqli_real_escape_string($dbConnection, $_POST['email']));
                            $pass++;
                        }else{
                            $error = "Sorry, the email you tried belongs to an existing account";
                        }
                    }else{
                        $email_error = "Invalid email address";
                    }
                }else{
                    $error = "You are only able to change your email once in every 2 days. " . $email_change_available_time->format("\O\\n l, \a\\t G:ia") . " you will be able to change again";
                    $confirmation_error = NULL; //User tried to change email before 2 days pass since last change, so no need to display confirmation error.
                }
            }else{
                $error = "You are already using this email: '$email'";
                $confirmation_error = NULL; //User submitted his/her own email, so no need to display confirmation error.
            }
        }else{
            $email_error = "Please enter the email you would like to use";
        }

        if($pass == 2){
            $query = "UPDATE USERS SET USER_EMAIL = '$user_email' WHERE USER_ID = '$user1_id'";
            mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

            $email_change_time = date("Y-m-d H:i:s");
            $query = "UPDATE SETTINGS SET SETTING_EMAIL_TIME = '$email_change_time' WHERE SETTING_USER = '$user1_id'";
            mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

            session_destroy();
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
            <script>Alert.render('Your email address has been successfully changed. Now, you will be redirected to the login page. Please, login again with your new email address!');</script>
            ";
        }else{
            $error_message_2 = "block";
        }
    }

    if($_GET["Change"] == "Password"){
        /* CURRENT PASSWORD */
        if(!empty($_POST['current_password'])){
            if(password_verify($_POST['current_password'], $password)){
                $current_password = mysqli_real_escape_string($dbConnection, $_POST['current_password']);
                $pass++;
            }else{
                $current_password_error = "Current password is not correct";
            }
        }else{
            $current_password_error = "Please enter your current password for confirmation to change your password";
        }

        /* NEW PASSWORD */
        if(!empty($_POST['new_password'])){
            if(strlen($_POST['new_password']) >= 6){
                /* RE-NEW PASSWORD */
                if(!empty($_POST['re_new_password']) && ($_POST['new_password'] == $_POST['re_new_password'])){
                    $user_password = crypt(mysqli_real_escape_string($dbConnection, $_POST['new_password']));
                    $pass++;
                }else{
                    $new_password_error = "Passwords does not match";
                }
            }else{
                $new_password_error = "Password should be at least 6 characters long";
            }
        }else{
            $new_password_error = "Please enter both new password fields";
        }

        if($pass == 2){
            $query = "UPDATE USERS SET USER_PASSWORD = '$user_password' WHERE USER_ID = '$user1_id'";
            mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

            session_destroy();
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
            <script>Alert.render('Your password has been successfully changed. Now, you will be redirected to the login page. Please, login again with your new password!');</script>
            ";
        }else{
            $error_message_2 = "block";
        }
    }

    if($_GET["Change"] == "Account"){
        /* PASSWORD */
        if(!empty($_POST['password']) && !empty($_POST['re_password'])){
            /* RE-PASSWORD */
            if(password_verify($_POST['password'], $password)){
                $pass++;
            }else{
                $error = "The password you entered is incorrect";
            }
        }else{
            $error = "Please enter both password fields";
        }

        if($pass == 1){
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
            <script>Confirm.render('Are you sure you want to freeze your account?', 'Freezing your account will remove everything (including your profile) to other user\'s access. Some information may still be visible others, such as your messages.<br /><br />Do you want to continue?', 'Freeze', '$user1_id')</script>
            ";
        }else{
            $error_message_2 = "block";
        }
    }
}
?>
    <div id="container" class="clearfix">
        <?php require_once("./include/sidebar.php");?>
        <div id="content">
            <div class="title">UPDATE YOUR PROFILE INFORMATION</div>
            <div class="content-box clearfix">
                <div class="settings-left">
                    <form action="/settings?Change=Picture" method="post" enctype="multipart/form-data">
                        <div class="subtitle">Change Profile Picture</div>
                        <div class="content-section">
                            <table>
                                <tr>
                                    <th rowspan="2"><img src="/image/profile/<?php echo $picture = ($picture != NULL ? $picture : "Default.png"); ?>" ></th>
                                    <td valign="top">
                                        <div class="button-5">
                                            <span>Choose Picture</span>
                                            <input type="file" id="picture" name="picture" class="upload">
                                        </div>
                                    </td>
                                    <td valign="top">
                                        <input class="settings-input" type="text" id="picture-path" placeholder="Choose Picture" disabled>
                                        <script>
                                            document.getElementById("picture").onchange = function (){
                                                document.getElementById("picture-path").value = this.value;
                                            };
                                        </script>
                                    </td>
                                </tr>
                                <tr valign="bottom"><td><input type="submit" class="button-6" name="remove" value="Remove Picture"></td></tr>
                            </table>
                        </div>
                        <input type="submit" class="button-4" name="submit" value="Upload Picture">
                    </form>

                    <form action="/settings?Change=Info" method="post">
                        <div class="subtitle">Change Profile Information</div>
                        <div class="content-section">
                            <label>
                                <span>First Name:</span>
                                <input class="settings-input" name="first_name" type="text" autocomplete="off" value="<?php echo $first_name; ?>">
                            </label>
                            <label>
                                <span>Last Name:</span>
                                <input class="settings-input" name="last_name" type="text" autocomplete="off" value="<?php echo $last_name; ?>">
                            </label>
                            <label>
                                <span>Display Name:</span>
                                <input class="settings-input" name="display_name" type="text" autocomplete="off" value="<?php echo $display_name; ?>">
                            </label>
                            <label>
                                <span>School:</span>
                                <select class="settings-input" name="school" id="school">
                                    <option value="Select Your School" disabled selected>Select Your School</option>
                                    <?php
                                    /* Get the school names from database
                                     * Get user 1's school name
                                     * Start outputting school names from database into drop down
                                     * If any of the school names matches with user 1's school, mark it as selected
                                     * Now, either user 1 sees his/her school as selected or sees 'Select Your School' because
                                     * he/she haven't been chosen any school from the list yet.
                                     * */

                                    /* GET SCHOOL LIST */
                                    $school_names = array();

                                    $query = "SELECT SCHOOL_NAME FROM SCHOOLS ORDER BY SCHOOL_TYPE ASC"; // Universities comes first
                                    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

                                    while($data = mysqli_fetch_assoc($result)){ $school_names[] = $data['SCHOOL_NAME']; }

                                    /* GET USER 1'S SCHOOL NAME */
                                    $query = "SELECT USER_SCHOOL FROM USERS WHERE USER_ID = '$user1_id'";
                                    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

                                    while($data = mysqli_fetch_assoc($result)){ $user_school = $data['USER_SCHOOL']; }

                                    /* OUTPUT SCHOOL LIST INTO DROP DOWN */
                                    foreach($school_names as $school_name){
                                        if($user_school == $school_name){
                                            echo "<option value='$school_name' selected>$school_name</option>";
                                        }else{
                                            echo "<option value='$school_name'>$school_name</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </label>
                            <label>
                                <span>Program:</span>
                                <select class="settings-input" name="program" id="program">
                                    <script>
                                        /* GET PROGRAM NAMES ACCORDING TO SCHOOL NAME */
                                        $(document).ready(function(){
                                            $("#school").change(function(){
                                                $("#program").load("lib/get-dropdown.php?School=" + encodeURIComponent($("#school").val()));
                                            }).change();
                                        });
                                    </script>
                                </select>
                            </label>
                            <label>
                                <span>Website:</span>
                                <input class="settings-input" name="website" type="text" autocomplete="off" value="<?php echo $website; ?>">
                            </label>
                        </div>
                        <input type="submit" class="button-4" name="submit" value="Save Changes">
                    </form>
                </div>
                <div class="settings-right">
                    <div class="info">
                        <div class="info-wrapper">
                        <span>A profile page supports only one picture for each user.
                        Uploading a new picture will overwrite the old one. (You don't necessarily need to remove)</span>
                        <span>You should choose your school from the list in order to choose a program</span>
                        </div>
                    </div>
                    <div class="error" style="display:<?php echo $error_message; ?>">
                        <div class="error-wrapper">
                           <?php echo $error; ?>
                            <span><?php echo $first_name_error; ?></span>
                            <span><?php echo $last_name_error; ?></span>
                            <span><?php echo $display_name_error; ?></span>
                            <span><?php echo $user_school_error; ?></span>
                            <span><?php echo $user_program_error; ?></span>
                            <span><?php echo $website_error ?></span>
                            <?php echo $picture_error; ?>
                        </div>
                    </div>
                    <?php
                    if(isset($_GET['Success'])){
                        switch($_GET['Success']){
                            case "Info":
                                $success = "<p class='message-title'>Congratulations!</p>
                                <p>Your information has been successfully changed</p>";
                                break;
                            case "PictureUpload":
                                $success = "<p class='message-title'>Congratulations!</p>
                                <p>Your picture has been successfully changed</p>";
                                break;
                            case "PictureRemove":
                                $success = "<p class='message-title'>Congratulations!</p>
                                <p>Your picture has been successfully removed</p>";
                                break;
                            default:
                                $success_message = "none";
                        }
                    }
                    ?>
                    <div class="success"  style="display:<?php echo $success_message; ?>">
                        <div class="success-wrapper">
                            <?php echo $success; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="title">GENERAL ACCOUNT SETTINGS</div>
            <div class="content-box clearfix">
                <div class="settings-left">
                    <form action="/settings?Change=Email" method="post">
                        <div class="subtitle">Change Email</div>
                        <div class="content-section">
                            <label>
                                <span>Email:</span>
                                <input class="settings-input" name="email" type="text" autocomplete="off" value="<?php echo $email; ?>">
                            </label>
                            <label>
                                <span>Password <i>(Confirmation)</i>:</span>
                                <input class="settings-input" name="password" type="password" autocomplete="off">
                            </label>
                        </div>
                        <input type="submit" class="button-4" name="submit" value="Save Changes">
                    </form>

                    <form action="/settings?Change=Password" method="post">
                        <div class="subtitle">Change Password</div>
                        <div class="content-section">
                            <label>
                                <span>Current Password:</span>
                                <input class="settings-input" name="current_password" type="password" autocomplete="off">
                            </label>
                            <label>
                                <span>New Password:</span>
                                <input class="settings-input" name="new_password" type="password" autocomplete="off">
                            </label>
                            <label>
                                <span>Re-type New Password:</span>
                                <input class="settings-input" name="re_new_password" type="password" autocomplete="off">
                            </label>
                        </div>
                        <input type="submit" class="button-4" name="submit" value="Save Changes">
                    </form>

                    <form action="/settings?Change=Account" method="post">
                        <div class="subtitle">Freeze This Account</div>
                        <div class="content-section">
                            <label>
                                <span>Password:</span>
                                <input class="settings-input" name="password" type="password" autocomplete="off">
                            </label>
                            <label>
                                <span>Re-type Password:</span>
                                <input class="settings-input" name="re_password" type="password" autocomplete="off">
                            </label>
                        </div>
                        <input type="submit" class="button-4" name="submit" value="Freeze Account">
                    </form>
                </div>
                <div class="settings-right">
                    <div class='info'>
                        <div class='info-wrapper'>
                            <span>You can change your email only once in every two days.</span>
                            <span>After you freeze this account, you can activate it back anytime by signing into
                                your frozen account.</span>
                        </div>
                    </div>
                    <div class="error" style="display:<?php echo $error_message_2; ?>">
                        <div class="error-wrapper">
                            <?php echo $error ?>
                            <span><?php echo $current_password_error; ?></span>
                            <span><?php echo $new_password_error; ?></span>
                            <span><?php echo $email_error; ?></span>
                            <span><?php echo $confirmation_error; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
require_once("./include/footer.php");
?>