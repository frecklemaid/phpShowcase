<!--TODO make universal CSS file-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("connection.php");
require_once ("Auth/authenticate.php");

if (isset($_GET['action']) && $_GET['action'] == 'logout.php') {
    session_destroy();
    //TODO get logout.php to redirect to login page
//    $loc = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'login.php';
//    Header("Location: $loc1");
    echo "<h3>You are logged out successfully. </h3>";
    echo "<a href='./Auth/login.php'>Login</a>";}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar navbar-dark bg-primary">
    <a class="navbar-brand" href="#">N. Altman Recruiting</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Home<span class="sr-only">(current)</span>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="dash.php">Dash<span class="sr-only">(current)</span></a>
                    <a class="dropdown-item" href="index.php">Customer Home</a>
                    <a class="dropdown-item" href="/Contact/contactForm.php">Contact Form</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Clients">Clients</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Contact">Contacts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Organisations">Organisations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Projects">Projects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Users">Users</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href='Auth/logout.php'>Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <!--Dashboard Homepage-->
    <h1 class="text-center display-6" id="dash-heading">Welcome to Your Dashboard!</h1>
    <?php
    //Fetch Users' First Name for Welcome Message
    $user_id = $_SESSION['user_id'];
    $nameQuery = "SELECT fname FROM `users` where id = :user_id";
    $nameStmt = $dbh->prepare($nameQuery);
    $nameStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $nameStmt->execute();
    $name = $nameStmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="center-container">
        <div class="dash-circle">
            <div class="dash-circle-inner">
                <span class="text-center"><?= $name['fname']?></span>
            </div>
        </div>
    </div>
        <div id="buttons">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-light btn-block" style="background-color: #f86e87" onclick="window.location.href= 'Clients'">Clients</button>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-light btn-block" style="background-color: #f86e87" onclick="window.location.href='Organisations'">Organisations</button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <button type="button" class="btn btn-light btn-block" style="background-color: #f86e87" onclick="window.location.href='Projects'">Projects</button>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-light btn-block" style="background-color: #f86e87" onclick="window.location.href='Users'">Users</button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-light btn-block" style="background-color: #f86e87" onclick="window.location.href='Contact'">Contacts</button>
                </div>
            </div>
        </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
