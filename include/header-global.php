<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . "/lib/config.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-database.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-common.php");
$dbConnection = open_connection();
?>

<html>
<head>
    <title>Welcome to Student Valley - Everything You Need As A student</title>
    <meta name="viewport" content="width=1000, initial-scale=1">
    <script type="text/javascript" src="/js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="/js/lightbox.min.js"></script>
    <link type="text/css" rel="stylesheet" href="/css/reset.css">
    <link type="text/css" rel="stylesheet" href="/css/lightbox.css">
    <link type="text/css" rel="stylesheet" href="/css/header-global.css">
    <link type="text/css" rel="stylesheet" href="/css/index.css">
    <link type="text/css" rel="stylesheet" href="/css/footer.css">
</head>
<body>
<div id="global-container">
    <div id="header">
        <div id="header-wrapper" class="clearfix">
            <div id="header-logo">
                <a href="http://studentvalley.org" title="Go to Student Valley">Student Valley</a>
            </div>
            <div id="header-login">
                <form action="/login" method="post" id="login-form">
                    <table>
                        <tr>
                            <td class="table-header"><label for="email">Email</label></td>
                            <td class="table-header"><label for="password">Password</label></td>
                        </tr>
                        <tr>
                            <td><input type="email" class="login-box" id="email" name="email"></td>
                            <td><input type="password" class="login-box" id="password" name="password"></td>
                            <td><input type="submit" class="button" name="submit" value="Log In"></td>
                        </tr>
                        <tr>
                            <td class="table-data"><label><input type="checkbox" class="checkbox" id="remember">Remember me</label></td>
                            <td class="table-data"><a href="/forgot">Forgot your password?</a></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

