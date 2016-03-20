<?php
session_start();
if($_SESSION['email']){
    session_destroy();
    header("Location: http://studentvalley.org");
}else{
    header("Location: http://studentvalley.org");
}