<!--Login Function-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body class="frontend-body">
<div id="box">
    <header>
        <h1 class="display-1 text-center" id="pink-title">Log In</h1>
    </header>
    <!-- Hashed username and password validation-->
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            //Run some SQL query here to find that user - password is hashed at database server
            $loginQuery = "SELECT * FROM `users` WHERE `username` = :username AND `password` = SHA2(:password, 0)";
            $LoginStmt = $dbh->prepare($loginQuery);

            if ($LoginStmt->execute([
                    'username' => $_POST['username'],
                    'password' => $_POST['password']
                ]) && $LoginStmt->rowCount() == 1) {
                // When the user is found, grab its id and store it into the session for future reference
                $row = $LoginStmt->fetchObject();
                $_SESSION['user_id'] = $row->id;
                //Successfully logged in, redirect user to dashboard
                header("Location: ../dash.php");
            } else {
                echo '<div class="alert alert-danger"><p>The username or password is incorrect</p></div>';
            }
        }
        else {
            echo '<div class="alert alert-danger"><p>Please enter you username and/or password.</p></div>';
        }
    }
    ?>
    <form class="frontend-forms" method="POST">
        <div class="form-group row mb-3">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="John" maxlength="128">
        </div>
        <div class="form-group row mb-3">
            <label for="password">Password</label>
            <!--Passwords must be at least 6 characters long and a maxlength of 128 characters-->
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" pattern=".{6,}" maxlength="128">
        </div>
        <div class="form-group row mb-3">
        <button type="submit" class="btn btn-primary" id="send" value="Login">Login</button>
        </div>
    </form>
</div>


<footer>
</footer>
</body>
</html>
