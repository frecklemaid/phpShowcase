ph<!--Users Addition Function-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once ("../Auth/authenticate.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['fname'] && !empty($_POST['lname']) && !empty($_POST['email']))) {
        $username = $_POST['username'];

        // Check if the username is already in use
        $uniqueUserQuery = "SELECT COUNT(*) FROM users WHERE username = :username";
        $uniqueUserStmt = $dbh->prepare($uniqueUserQuery);
        $uniqueUserStmt->bindParam(':username', $username, PDO::PARAM_STR);
        $uniqueUserStmt->execute();

        $count = $uniqueUserStmt->fetchColumn();

        if ($count > 0) {
            // Username is already taken
            echo '<div class="alert alert-danger"><p>Username is not available. Please choose another one.</p></div>';
        }
        else {
            //Run some SQL query here to find that user
            $userInsertQuery = "INSERT INTO `users`(`username`, `password`, `fname`, `lname`, `email`) VALUES (:username, SHA2(:password, 0), :fname, :lname, :email)";
            $userInsertStmt = $dbh->prepare($userInsertQuery);
            if ($userInsertStmt->execute([
                'username' => $_POST['username'],
                'password' => $_POST['password'], //Hash the inputted password
                'fname' => $_POST['fname'],
                'lname' => $_POST['lname'],
                'email' => $_POST['email']
            ])) {
                header("Location: index.php");
            } else {
                echo "<div class='alert alert-danger'></div><h1>Cannot register!</h1><div>Error message: " . $userInsertStmt->errorInfo()[2] . "</div></div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Create User</title>
</head>
<body>
<div class="form-container">
    <header>
        <h1>Create User</h1>
    </header>
    <form method="POST" action="">
        <div class="form-group">
            <label for="fname">First Name</label>
            <!--Names must include only letters-->
            <input type="text" name="fname" id="fname" class="form-control" title="Names must include only letters - no special characters" maxlength="50" pattern="[A-Za-z]+" placeholder="John" required>
        </div>
        <div class="form-group">
            <label for="lname">Last Name:</label>
            <!--Names must include only letters-->
            <input type="text" name="lname" id="lname" class="form-control" title="Names must include only letters - no special characters" maxlength="50" pattern="[A-Za-z]+" placeholder="Doe" required>
        </div>
        <div class="form-group">
            <label for="username">Usermame:</label>
            <!--Usernames Must Be a Minimum of 3 and Maximum of 128 Characters-->
            <input type="text" name="username" id="username" class="form-control" title="Username must be between 3 and 128 characters long" maxlength="128" minlength="3" placeholder="jdoe" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <!--Passwords must be at least 6 characters long and a maxlength of 128 characters-->
            <input type="password" class="form-control" id="password" name="password" title="Password must be between 6 and 128 characters" placeholder="Password" minlength="6" maxlength="128">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <!--Email must follow email pattern-->
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" maxlength="255"  pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" required>
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="../Users/index.php" class="btn btn-success buttons">Cancel</a>

    </form>
</div>
</body>
</html>
