<?php
ob_start();
session_start();
if(isset($_SESSION['email'])){header("location: /dashboard");}
require_once("./include/header-global-2.php");

$errorMessage = "none";

if($_POST){
    $userEmail = mysqli_real_escape_string($dbConnection, $_POST['email']);
    $userPassword = mysqli_real_escape_string($dbConnection, $_POST['password']);
    $userEmail_db = NULL;
    $userPassword_db = NULL;
    $error = NULL;

    $query = "SELECT * FROM USERS WHERE USER_EMAIL = '$userEmail'";

    $result = mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));
    $num_rows = mysqli_num_rows($result);

    if($num_rows != 0){
        while($row = mysqli_fetch_assoc($result)){
            $userEmail_db = $row['USER_EMAIL'];
            $userPassword_db = $row['USER_PASSWORD'];
        }
        if($userEmail == $userEmail_db && password_verify($userPassword, $userPassword_db)){
            $_SESSION['email'] = $userEmail_db;

            $user1_id = return_user_id($dbConnection, $_SESSION['email']);
            $query = "UPDATE SETTINGS SET SETTING_FREEZE = 'No' WHERE SETTING_USER = '$user1_id'";
            mysqli_query($dbConnection, $query) or die(mysqli_error($dbConnection));

            header('Location: /dashboard');
        }else{
            $error = "<p class='message-title'>Incorrect Password</p>
                  <p>The password you entered is incorrect. If you forgot your password you can
                  <a href='/forgot'><b>try to reset</b></a>. <u>Please make sure your caps lock is off</u>.</p>";
            $errorMessage = "block";
        }
    }else{
        $error = "<p class='message-title'>Incorrect Email</p>
                  <p>The email you entered does not belong to any account. <u>Please make sure you typed correctly</u>.</p>";
        $errorMessage = "block";
    }
}
?>
<div id="body-lsfr">
    <div id="body-wrapper">
        <div id="form">
            <div id="form-title">Student Valley Login</div>
            <div id="error" style="display:<?php echo $errorMessage ?>">
                <div id="error-wrapper"><span <?php echo "style='background: none; padding-left: 0;'"; ?>><?php echo $error ?></span></div>
            </div>
            <div id="form-wrapper">
                <div class="logo"></div>
                <form action="/login" method="post" id="login-form">
                    <label for="email">Email</label>
                    <input type="email" class="input-box" id="email" name="email">

                    <label for="password">Password</label>
                    <input type="password" class="input-box" id="password" name="password">

                    <label><input type="checkbox" class="checkbox" id="remember"><span>Remember me</span></label>

                    <div id="buttons">
                        <input type="submit" class="button" name="submit" value="Log In">
                        <a class="link" href="/forgot"><span>Forgot your password?</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
require_once("./include/footer.php");
?>