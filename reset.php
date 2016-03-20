<?php
ob_start();
session_start();
require_once("./include/header-global-2.php");

if(isset($_GET['Code'])){
    $code = mysqli_real_escape_string($dbConnection, $_GET['Code']);
}else{
    header('Location: http://studentvalley.org');
}

$success_message = "none";
$error_message = "none";
$reset_form = "block";
$home_button = "none";
$logo = "none";

/* Checking if any request exist with the code provided in the link */
$query = "SELECT * FROM TOKENS WHERE TOKEN_CODE = '$code'";
$result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

while($data = mysqli_fetch_assoc($result)){
    $tokenDate = $data['TOKEN_DATE'];
    $token_owner = $data['TOKEN_OWNER'];
}

if($token_owner == NULL){
    $home_button = "block";
    $logo = "block";
    $error_message = "block";

    $error = "<p class='message-title'>There is no reset request for this link</p>
                <p>The password reset link you are trying has already expired or never created. You should reset your password after 1 hour when you received password reset email.
                If you are sure that link has not expired, <a href='/contact'><b>contact us</b></a> with the email you tried to reset
                and the following error code inside the email content.</p>
                <p><b>Error Code: 1002</b></p>";
    ?>
    <div id="body-lsfr">
        <div id="body-wrapper">
            <div id="form">
                <div id="form-title">Error!</div>
                <div class="logo-2"></div>
                <div id="error">
                    <div id="error-wrapper">
                        <span <?php echo "style='background: none; padding-left: 0;'"; ?>><?php echo $error ?></span>
                    </div>
                </div>
                <div class="home_button">
                    <button type="button" onclick="visitHomePage();" class="button-2">Go to Home Page</button>
                    <script>
                        function visitHomePage() {
                            window.location = "http://studentvalley.org"
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
<?php
}else{
    if($_POST){
        /* PASSWORD */
        if(!empty($_POST['password']) && !empty($_POST['rePassword'])){
            if($_POST['password'] == $_POST['rePassword']){
                if(strlen($_POST['password']) >= 6){
                    $user_password = crypt(trim(mysqli_real_escape_string($dbConnection, $_POST['password'])));
                    $pass++;
                    $new_password = crypt($_POST['password']);

                    /* Update the new password and delete the token from database */
                    $query = "UPDATE USERS SET USER_PASSWORD = '$new_password' WHERE USER_EMAIL = '$token_owner';";
                    $query .= "DELETE FROM TOKENS WHERE TOKEN_CODE = '$code';";

                    $result = mysqli_multi_query($dbConnection, $query) or die(mysqli_error($dbConnection));
                    if($result){
                        $success = "<p class='message-title'>Congratulations! You can log in with new password</p>
                                <p>Your password has been successfully changed. You can go home page and log in with your new password.</p>";
                        $success_message = "block";
                    }else{
                        $error = "<p class='message-title'>Oops! Something Went Wrong</p>
                    <p>We were not able to reset your password. Please try to reset your password again.
                    If you are seeing this message for the second time, <a href='/contact'><b>contact us</b></a> with the email you tried to reset
                    and the following error code inside the email content.</p>
                    <p><b>Error Code: 1001</b></p>";
                        $error_message = "block";
                    }
                    $reset_form = "none";
                    $home_button = "block";
                    $logo = "block";
                }else{
                    $error = "<p class='message-title'>Passwords is Too Short</p>
                        <p>Password should be at least 6 characters long. Please try again. <u>Please make sure you typed slowly and correctly</u>.</p>";
                    $error_message = "block";
                }
            }else{
                $error = "<p class='message-title'>Passwords Do Not Match</p>
                  <p>Passwords you entered do not match. Please try again. <u>Please make sure you typed slowly and correctly</u>.</p>";
                $error_message = "block";
            }
        }else{
            $error = "<p class='message-title'>Fill Up All Fields</p>
                  <p>Fields cannot be empty. Please enter your new password or if you remember your password
                  you can go back to login page by clicking 'No! I remember my password'.</p>";
            $error_message = "block";
        }
    }
    ?>
    <div id="body-lsfr">
        <div id="body-wrapper">
            <div id="form">
                <div id="form-title">Password Reset</div>
                <div class="logo-2" style="display:<?php echo $logo; ?>"></div>
                <div id="success" style="display:<?php echo $success_message; ?>">
                    <div id="success-wrapper">
                        <span><?php echo $success ?></span>
                    </div>
                </div>
                <div id="error" style="display:<?php echo $error_message; ?>">
                    <div id="error-wrapper">
                        <span <?php echo "style='background: none; padding-left: 0;'"; ?>><?php echo $error ?></span>
                    </div>
                </div>
                <div class="home_button" style="display:<?php echo $home_button; ?>">
                    <button type="button" onclick="visitHomePage();" class="button-2">Go to Home Page</button>
                    <script>
                        function visitHomePage() {
                            window.location = "http://studentvalley.org"
                        }
                    </script>
                </div>
                <div id="form-wrapper" style="display:<?php echo $reset_form; ?>">
                    <div class="logo"></div>
                    <form action="/reset?Code=<?php echo $code;?>" method="post" id="reset-form">
                        <label for="password">New Password</label>
                        <input type="password" class="input-box" id="password" name="password">

                        <label for="rePassword">Re-enter New Password</label>
                        <input type="password" class="input-box" id="rePassword" name="rePassword">

                        <div id="buttons">
                            <input type="submit" class="button" name="submit" value="Change">
                            <a class="link" href="/login"><span>No! I remember my password</span></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}
require_once("./include/footer.php");
?>