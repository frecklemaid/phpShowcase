<!--Users Modification Function-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if (isset($_GET['id'])) {
    // Fetch user details for editing
    $user_id = $_GET['id'];
    $userQuery = "SELECT * FROM users WHERE id=?";
    $userStmt = $dbh->prepare($userQuery);
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            //Retrieve values from the form
            $new_username = $_POST['username'];
            $new_fname = $_POST['fname'];
            $new_lname = $_POST['lname'];
            $new_email = $_POST['email'];

            // Perform the database update sans password
            $updateQuery = "UPDATE users
                SET username=:new_username, fname=:new_fname, lname=:new_lname, email=:new_email
                WHERE id=:user_id";
            $updateStmt = $dbh->prepare($updateQuery);
            $updateStmt->bindParam(':new_username', $new_username, PDO::PARAM_STR);
            $updateStmt->bindParam(':new_fname', $new_fname, PDO::PARAM_STR);
            $updateStmt->bindParam(':new_lname', $new_lname, PDO::PARAM_STR);
            $updateStmt->bindParam(':new_email', $new_email, PDO::PARAM_STR);
            $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $updateStmt->execute();


            //Handle password update if new password inputted
            if ($_POST['password'] != $user['password']) {
                $new_raw_password = $_POST['password'];
                //Has the new inputted password
                $new_password = hash('sha256', $new_raw_password);
                // Update the password
                $passUpdateQuery = "UPDATE users SET password=:new_password WHERE id=:user_id";
                $passUpdateStmt = $dbh->prepare($passUpdateQuery);
                $passUpdateStmt->bindParam(':new_password', $new_password, PDO::PARAM_STR);
                $passUpdateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $passUpdateStmt->execute();
            }
            // Redirect to the user list page after updating the user
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Display any database errors
        }
    }
} else {
    // If there is no 'id' parameter in the URL, redirect to the user list page
    header("Location: index.php");
    exit();
}
?>

<!-- Update User Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Update User</title>
</head>
<body>
<div class="form-container">
    <h1>Update User</h1>
    <form method="POST" action="">
        <div class="form-group">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
        </div>
        <div class="form-group">
            <!--Usernames Must Be a Minimum of 3 and Maximum of 128 Characters-->
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" title="Username must be between 3 and 128 characters long" maxlength="128" minlength="3" placeholder="jdoe" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <!--Passwords must be at least 6 characters long and a maxlength of 128 characters-->
            <label for="password">Password:</label>
            <!--Password is not preloaded as a hashed password is not useful anyway-->
            <input type="password" name="password" class="form-control" title="Password must be between 6 and 128 characters" placeholder="Password" minlength="6" maxlength="128" value="<?php echo $user['password']; ?>">
        </div>
        <div class="form-group">
            <!--Names must include only letters-->
            <label for="fname">First Name:</label>
            <input type="text" name="fname" class="form-control" title="Names must include only letters - no special characters" maxlength="50" pattern="[A-Za-z]+" placeholder="Doe" value="<?php echo $user['fname']; ?>" required>
        </div>
        <div class="form-group">
            <!--Names must include only letters-->
            <label for="lname">Last Name:</label>
            <input type="text" name="lname" class="form-control" title="Names must include only letters - no special characters" maxlength="50" pattern="[A-Za-z]+" placeholder="Doe" value="<?php echo $user['lname']; ?>" required>
        </div>
        <div class="form-group">
            <!--Email must follow email pattern-->
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" placeholder="Enter email" maxlength="255"  pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="<?php echo $user['email']; ?>" required>
        </div>
        <button type="submit" class="btn btn-danger buttons">Update User</button>
        <a href="../Users/index.php" class="btn btn-success buttons">Cancel</a>
    </form>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
