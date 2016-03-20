<?php
session_start();
if($_SESSION['email']){ header("location: /dashboard"); } //If user is logged in, don't display index.php
require_once("./include/header-global.php");

/* If the email that is used for registration is same, signup.php will return error
 * According to that, this function will decide to display the error message or not.
 * */
if(isset($_GET['error'])){
    $error = "block";
}else{
    $error = "none";
}
?>
<div id="body">
    <div id="body-wrapper" class="clearfix">
        <div id="headline">Student Valley is a network for student needs</div>
        <div id="content-a">
            <div id="image-1" class="statement">
                <div class="title">Information about courses</div>
                <div class="definition">Find out everything about the course before you take it.</div>
            </div>
            <div id="image-2" class="statement">
                <div class="title">Get help on your assignments, homework and essays</div>
                <div class="definition">Ask questions to other students in your program or get other student's study notes.</div>
            </div>
            <div id="image-3" class="statement">
                <div class="title">Sell, trade or rent your books</div>
                <div class="definition">A new book is expensive? Come in and see who has the book you want.</div>
            </div>
            <div id="image-4" class="statement">
                <div class="title">Professor reviews based on student's feedback</div>
                <div class="definition">Find out who is teaching great or who is high marker ;)</div>
            </div>
            <div id="image-5" class="statement">
                <div class="title">Find new roommates</div>
                <div class="definition">Looking for a new place? or new roommates? Come in and find out.</div>
            </div>
        </div>
        <div id="content-b">
            <table>
                <caption>Take a look inside</caption>
                <tr>
                    <td><a href="/image/sv/Screenshot.png" data-lightbox="image"><img src="/image/sv/Screenshot.png"></a></td>
                    <td><a href="/image/sv/Screenshot.png" data-lightbox="image"><img src="/image/sv/Screenshot.png"></a></td>
                </tr>
                <tr>
                    <td><a href="/image/sv/Screenshot.png" data-lightbox="image"><img src="/image/sv/Screenshot.png"></a></td>
                    <td><a href="/image/sv/Screenshot.png" data-lightbox="image"><img src="/image/sv/Screenshot.png"></a></td>
                </tr>
                <tr>
                    <td><a href="/image/sv/Screenshot.png" data-lightbox="image"><img src="/image/sv/Screenshot.png"></a></td>
                    <td><a href="/image/sv/Screenshot.png" data-lightbox="image"><img src="/image/sv/Screenshot.png"></a></td>
                </tr>
            </table>
        </div>
    </div>
    <div id="content-c">
        <div id="content-c-wrapper">
            <form action="/signup" method="post" name="signup-form">
                <table>
                    <div id="caption">New to Student Valley? Sign Up - It's Free</div>
                    <tr>
                        <td class="table-header"><label for="firstName">First Name</label></td>
                        <td class="table-header"><label for="lastName">Last Name</label></td>
                        <td class="table-header"><label for="email">Email</label></td>
                        <td class="table-header"><label for="reEmail">Re-enter Email</label></td>
                        <td class="table-header"><label for="password">Password</label></td>
                    </tr>
                    <tr>
                        <td><input type="text" class="signup-box" id="firstName" name="firstName"></td>
                        <td><input type="text" class="signup-box" id="lastName" name="lastName"></td>
                        <td><input type="email" class="signup-box" id="email" name="email"></td>
                        <td><input type="email" class="signup-box" id="reEmail" name="re-email"></td>
                        <td><input type="password" class="signup-box" id="password" name="password"></td>
                        <td><input type="submit" class="button" name="submit" value="Sign Up"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php
require_once("./include/footer.php");
?>