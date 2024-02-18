<!-- Authenticate.php handles authentication for pages where it is necessary to check whether the user is logged in-->

<?php

session_start();

global $dbh;
require_once(__DIR__ . '/../connection.php');

//Absolute path of the login.php file so that it can be used universally
$loginDir = dirname(APP_URL_PATH) . DIRECTORY_SEPARATOR . 'Auth' . DIRECTORY_SEPARATOR . 'login.php';
$dashDir = dirname(APP_URL_PATH) . DIRECTORY_SEPARATOR . 'dash.php';

//Does the session already have a valid user ID?
if (isset($_SESSION['user_id'])) {
    $sessionStmt = $dbh->prepare("SELECT * FROM `users` WHERE `id` = ?");
    if ($sessionStmt->execute([$_SESSION['user_id']]) && $sessionStmt->rowCount() == 1) {
        //Check if the user still has a valid account in the database
        $user = $sessionStmt->fetchObject();

        if ($_SERVER['SCRIPT_NAME'] === $loginDir) {
            header("Location: $dashDir");
            exit();
        }
    } else {
        //if the user account is no longer valid, log the user out and send to login form
        session_destroy();
        Header("Location: $loginDir");
    }
} else {
    //If there is no current logged in session, redirect user to the login form
    if ($_SERVER['SCRIPT_NAME'] !== $loginDir) {
        header("Location: $loginDir");
        exit();
    }
}
?>
