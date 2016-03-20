<?php
ob_start();
session_start();
require_once("./include/header-global-2.php");

$infoMessage = "block";
$successMessage = "none";
$errorMessage = "none";
$forgotForm = "block";
$logo = "none";
$homeButton = "none";

if($_POST){
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $result = user_exist($dbConnection, $_POST['email']);
        if($result){
            $query = "SELECT USER_FIRST_NAME, USER_LAST_NAME FROM USERS WHERE USER_EMAIL = '$_POST[email]'";
            $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
            while($data = mysqli_fetch_assoc($result)){
                $firstName = $data['USER_FIRST_NAME'];
                $lastName = $data['USER_LAST_NAME'];
            }

            $code = "sv" . md5(uniqid(date("Y-m-d H:i:s"), true) * rand()); // Create unique id for token.
            $owner = $_POST['email'];
            $date = date("Y-m-d H:i:s");

            $to = "{$firstName} {$lastName} <$_POST[email]>";
            $subject = "You requested password reset for your Student Valley account";
            /* Passing variables to the link */
            $body = file_get_contents("http://www.studentvalley.org/layout/reset-email.php?Firstname=".$firstName."&Lastname=".$lastName."&Code=".$code);
            $headers = "From: Student Valley <reset@studentvalley.org>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $result = mail($to, $subject, $body, $headers);
            if($result){
                $success = "<p class='message-title'>Congratulations! Please check your email</p>
                            <p>We have sent a password reset link to $_POST[email]. If you don't see the email, check your junk or spam folder.</p>'
                            <p>If you find it there, please mark the email as 'Not Junk'.</p>";
                $successMessage = "block";

                $query = "INSERT INTO TOKENS (TOKEN_OWNER, TOKEN_CODE, TOKEN_DATE) VALUES ('$owner', '$code', '$date')";
                mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
            }else{
                $error = "<p class='message-title'>Oops! Something Went Wrong</p>
                        <p>We were not able to send a password reset link to your email. Please try to send a link again.
                        If you are seeing this message for the second time, <a href='/contact'><b>contact us</b></a> with the email you tried to reset
                        and the following error code inside the email content.</p>
                        <p><b>Error Code: 1000</b></p>";
                $errorMessage = "block";
            }
            $infoMessage = "none";
            $forgotForm = "none";
            $logo = "block";
            $homeButton = "block";
        }else{
            $error = "<p class='message-title'>Email Does Not Exist</p>
                  <p>The email you entered is not registered in our system. Make sure that it is typed correctly.</p>";
            $errorMessage = "block";
        }
    }else{
        $error = "<p class='message-title'>Invalid Email Format</p>
                  <p>The email you entered has invalid format. Make sure that it is typed correctly.</p>";
        $errorMessage = "block";
    }
}
?>
<div id="body-lsfr">
    <div id="body-wrapper">
        <div id="form">
            <div id="form-title">Forgot Your Password?</div>
            <div id="info" style="display:<?php echo $infoMessage; ?>">
                <div id="info-wrapper">
                    <span>Enter the email you used when you registered Student Valley</span>
                    <span>We will send you a link, where you can reset your password</span>
                </div>
            </div>
            <div class="logo-2" style="display:<?php echo $logo; ?>"></div>
            <div id="success" style="display:<?php echo $successMessage; ?>">
                <div id="success-wrapper">
                    <span><?php echo $success ?></span>
                </div>
            </div>
            <div id="error" style="display:<?php echo $errorMessage; ?>">
                <div id="error-wrapper"><span <?php echo "style='background: none; padding-left: 0;'"; ?>><?php echo $error ?></span></div>
            </div>
            <div class="home_button" style="display:<?php echo $homeButton; ?>">
                <button type="button" onclick="visitHomePage();" class="button-2">Go to Home Page</button>
                <script>
                    function visitHomePage(){ window.location="http://studentvalley.org"}
                </script>
            </div>
            <div id="form-wrapper" style="display:<?php echo $forgotForm; ?>">
                <div class="logo"></div>
                <form action="/forgot" method="post" id="forgot-form">
                    <label for="email">Email</label>
                    <input type="email" class="input-box" id="email" name="email">
                    <div id="buttons">
                        <input type="submit" class="button" name="submit" value="Continue">
                        <a class="link" href="http://studentvalley.org"><span>Cancel</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once("./include/footer.php");
?>