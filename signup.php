<?php
ob_start();
session_start();
require_once("./include/header-global-2.php");

$error_message = "none";
$user_first_name = NULL;
$user_last_name = NULL;
$user_email = NULL;
$user_re_email = NULL;
$user_password = NULL;
$pass = 0;

if($_POST){
    $result = user_exist($dbConnection, $_POST['email']);

    if($result){
        $error = "<p class='message-title'>Email Already Exist</p>
                  <p>Sorry, the email you tried belongs to an existing account. If it is you, <a href='/login'><b>try to log in</b></a>
                  or if you forgot your password <a href='/forgot'><b>try to reset</b></a>.</p>";
        $error_message = "block";
    }else{
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

        /* EMAIL */
        if(!empty($_POST['email'])){
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                /* RE-EMAIL */
                if(!empty($_POST['re_email']) && ($_POST['email'] == $_POST['re_email'])){
                    $user_email = trim(mysqli_real_escape_string($dbConnection, $_POST['email']));
                    $pass++;
                }else{
                    $email_error = "Email address does not match";
                }
            }else{
                $email_error = "Invalid email address";
            }
        }else{
            $email_error = "Please enter both email ";
        }

        /* PASSWORD */
        if(!empty($_POST['password'])){
            if(strlen($_POST['password']) >= 6){
                $user_password = crypt(trim(mysqli_real_escape_string($dbConnection, $_POST['password'])));
                $pass++;
            }else{
                $password_error = "Password should be at least 6 characters long";
            }
        }else{
            $password_error = "Please enter your password";
        }

        /* REGISTRATION DATE */
        $user_registration_date = date("Y-m-d H:i:s");

        if($pass == 4){ //If pass is 5, it means form is validated.
            echo "<script>alert('Registration system is close!\\nStudent Valley is under maintenance.');</script>";
            //UN-COMMENT BELOW SECTION TO MAKE USER REGISTRATION ACTIVE.
//            $query = "INSERT INTO USERS(";
//            $query .= "USER_FIRST_NAME, USER_LAST_NAME, USER_EMAIL, USER_PASSWORD, USER_REGISTRATION_DATE";
//            $query .= ")VALUES(";
//            $query .= "'{$user_first_name}', '{$user_last_name}', '{$user_email}', '{$user_password}', '{$user_registration_date}'";
//            $query .= ")";
//
//            $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
//            if(!$result){
//                $error = "<p class='message-title'>Oops! Something Went Wrong</p>
//                        <p>We were not able to sign you up for Student Valley. Please try to sign up again.
//                        If you are seeing this message for the second time, <a href='/contact'><b>contact us</b></a> with the email you tried to sign up
//                        and the following error code inside the email content.</p>
//                        <p><b>Error Code: 1003</b></p>";
//                $error_message = "block";
//            }else{ //Registration is successful. Create a row for the user in settings table.
//                $user_id = return_user_id($dbConnection, $user_email);
//
//                $query = "INSERT INTO SETTINGS(";
//                $query .= "SETTING_USER, SETTING_FREEZE, SETTING_EMAIL_DISPLAY";
//                $query .= ")VALUES(";
//                $query .= "'{$user_id}', 'No', 'Login Email'";
//                $query .= ")";
//
//                mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
//
//                $_SESSION['email'] = $user_email;
//                header("Location: /dashboard");
//            }
        }else{
            $error_message = "block";
        }
    }
}
?>
    <div id="body-lsfr">
        <div id="body-wrapper">
            <div id="form">
                <div id="form-title">Student Valley Sign Up</div>
                <div id="error" style="display:<?php echo $error_message; ?>">
                    <div id="error-wrapper">
                        <span <?php if(!isset($error)) echo "style='display:none'";else echo "style='background: none; padding-left: 0;'"; ?>><?php echo $error; ?></span>
                        <span <?php if(!isset($first_name_error)) echo "style='display:none'"; ?>><?php echo $first_name_error; ?></span>
                        <span <?php if(!isset($last_name_error)) echo "style='display:none'"; ?>><?php echo $last_name_error; ?></span>
                        <span <?php if(!isset($email_error)) echo "style='display:none'"; ?>><?php echo $email_error; ?></span>
                        <span <?php if(!isset($password_error)) echo "style='display:none'"; ?>><?php echo $password_error; ?></span>
                    </div>
                </div>
                <div id="form-wrapper">
                    <div class="logo"></div>
                    <form action="/signup" method="post" name="signup-form">
                        <label for="first_name">First Name</label>
                        <input type="text" class="input-box" id="first_name" name="first_name"
                               value="<?php echo $user_first_name ?>">

                        <label for="last_name">Last Name</label>
                        <input type="text" class="input-box" id="last_name" name="last_name"
                               value="<?php echo $user_last_name ?>">

                        <label for="email">Email</label>
                        <input type="email" class="input-box" id="email" name="email" value="<?php echo $user_email ?>">

                        <label for="re_email">Re-enter Email</label>
                        <input type="email" class="input-box" id="re_email" name="re_email"
                               value="<?php echo $user_re_email ?>">

                        <label for="password">Password</label>
                        <input type="password" class="input-box" id="password" name="password">

                        <div id="buttons">
                            <input type="submit" class="button" name="submit" value="Sign Up">
                            <a class="link" href="/login"><span>Log into an existing account</span></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
require_once("./include/footer.php");
?>