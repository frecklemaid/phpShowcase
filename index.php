<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">

</head>
<body id="home-body">
<div id="home-box">
    <div id="home-content">
        <header>
            <h1 class="display-1 text-center" id="pink-title">Nathan Altman</h1>
        </header>
    <div class="circles">
        <div class="circle">
            <div class="circle-inner">
                <span>Science</span>
            </div>
        </div>
        <div class="circle">
            <div class="circle-inner">
                <span>Recruitment</span>
            </div>
        </div>
        <div class="circle">
            <div class="circle-inner">
                <span>Opportunity</span>
            </div>
        </div>
        </div> <br> <br>
        <div id="index-buttons">
            <button type="button" class="btn btn-primary home-buttons" onclick="window.location.href = '/Auth/login.php'">Login</button>
            <button type="button" class="btn btn-primary home-buttons" onclick="window.location.href = '/Contact/contactForm.php'">Contact Me</button>
        </div>
    </div>
</div>
<footer>
</footer>
</body>
</html>
