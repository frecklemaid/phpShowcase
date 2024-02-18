<!--Delete User Information-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
//session_start();
global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if (isset($_GET['id'])) {
    // Fetch user details for confirmation
    $user_id = $_GET['id'];
    $userQuery = "SELECT * FROM users WHERE id=?";
    $userStmt = $dbh->prepare($userQuery);
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $userNotCurrent = $user_id != $_SESSION['user_id'];
    if ($userNotCurrent) {
        //Only allow user to be deleted if there is more than one user left
        //Do not allow user to delete themselves
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle the form submission to delete a user
            $id = $_POST['id'];

            $deleteQuery = "DELETE FROM users WHERE id=?";
            $deleteStmt = $dbh->prepare($deleteQuery);
            $deleteStmt->execute([$id]);

            // Redirect to the user list page after deleting the user
            header("Location: ../Users/index.php");
            exit();
        }
    } else if (!$userNotCurrent) {
        echo '<div class="alert alert-danger"><p>Cannot delete current user.</p></div>';

    }
    }
else {
    // If there is no 'id' parameter in the URL, redirect to Users/index.php
    header("Location: ../Users/index.php");
    exit();
}


?>



<!-- Delete User Confirmation (HTML) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Delete User</title>

</head>
<body>
<div class="delete-container text-center">
    <header>
        <h1>Delete User</h1>
    </header>
    <?php
    // If there is more than one user, then show the form. Else, show error message.
    if ($userNotCurrent) {
        echo '<h4>Are you sure you want to delete the user "' . $user['username'] . '"?</h4>';
        echo '<form method="POST" action="">';
        // Hidden Input of Selected user To Send to Delete PHP Execution
        echo '<input type="hidden" name="id" value="' . $user['id'] . '">';
        echo '<button type="submit" class="btn btn-danger delete-buttons" value="Delete">Delete</button>';
        // If the user cancels, go back to db list
        echo '<a href="../Users/index.php" class="btn btn-success delete-buttons">Cancel</a>';
        echo '</form>';
    }else if (!$userNotCurrent) {
        echo '<h4>You cannot delete the logged in user "' . $user['username'] . '". Please use another account session to delete this user.</h4>';
        echo '<a href="../Users/index.php" class="btn btn-success">Cancel</a>';
    }
    ?>

</div>
</body>
</html>
