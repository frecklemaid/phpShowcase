<?php
$db_hostname = "localhost";
$db_username = "main";
$db_password = "password";
$db_name = "fit2104_assignment_3";

$dsn = "mysql:host=$db_hostname;dbname=$db_name";
$dbh = new PDO($dsn, "$db_username", "$db_password");

function displayPDOError(PDOException $exception): void {
    // Set appropriate headers and HTTP response to display error message
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type:text/plain');

    // Show error messages of the exception when execution is failed
    echo $exception->getMessage();
}
//These will help with navigating directories in functions.
// the absolute path to the root of project folder
define("APP_FOLDER_PATH", __DIR__);
// and the URL full path to the root of the project
define("APP_URL_PATH", substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])));
