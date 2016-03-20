<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/lib/config.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-database.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-common.php");
$dbConnection = open_connection();

if(basename($_SERVER['PHP_SELF']) == "login.php"){
    $button_name = "Go to Sign Up Page";
    $action = "/signup";
}else{
    $button_name = "Go to Log In Page";
    $action = "/login";
}
?>

<html>
<head>
    <title>Welcome to Student Valley - Everything You Need As A student</title>
    <meta name="viewport" content="width=1000, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="/css/reset.css">
    <link type="text/css" rel="stylesheet" href="/css/header-global-2.css">
    <link type="text/css" rel="stylesheet" href="/css/one-column.css">
    <link type="text/css" rel="stylesheet" href="/css/footer.css">
</head>
<body>
<div id="global-container">
    <div id="header">
        <div id="header-wrapper" class="clearfix">
            <div id="header-logo">
                <a href="http://studentvalley.org" title="Go to Student Valley">Student Valley</a>
            </div>
            <div id="header-button">
                <button type="button" onclick="visitPage();" class="button-2"><?php echo $button_name; ?></button>
                <script>
                    function visitPage(){ window.location="<?php echo $action; ?>"; }
                </script>
            </div>
        </div>
    </div>
