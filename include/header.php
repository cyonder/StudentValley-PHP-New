<?php
session_start();
if(!isset($_SESSION['email'])){header("location: http://studentvalley.org");}

require($_SERVER['DOCUMENT_ROOT'] . "/lib/config.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-database.php");
require($_SERVER['DOCUMENT_ROOT'] . "/lib/functions-common.php");
$dbConnection = open_connection();
mysqli_set_charset($dbConnection, 'utf8mb4');

?>
<html>
<head>
    <title>Student Valley</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1000, initial-scale=1">
    <script type="text/javascript" src="/js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="/js/lightbox.min.js"></script>
    <link type="text/css" rel="stylesheet" href="/css/reset.css">
    <link type="text/css" rel="stylesheet" href="/css/lightbox.css">
    <link type="text/css" rel="stylesheet" href="/css/header.css">
    <link type="text/css" rel="stylesheet" href="/css/sidebar.css">
    <link type="text/css" rel="stylesheet" href="/css/two-column.css">
    <link type="text/css" rel="stylesheet" href="/css/footer.css">
</head>
<body>
<div id="global-container">
    <div id="header">
        <div id="header-wrapper" class="clearfix">
            <div id="header-logo">
                <a href="/dashboard" title="Go to Dashboard">Student Valley</a>
            </div>
            <div id="header-search">
                <form action="/search" method="post" id="header-search-form">
                    <input type="text" name="search" placeholder="Press enter to search...">
                </form>
            </div>
            <div id="header-menu">
                <ul>
                    <li><a href="/profile">Profile</a></li>
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a href="/messages">Messages</a></li>
                    <li><a href="/logout">Log Out</a></li>
                </ul>
            </div>
        </div>
    </div>
