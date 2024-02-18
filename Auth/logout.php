<!--Logout Page shown after user selects logout from navbar-->
<?php
// Start the session
session_start();

//Initialise $logoutNotSuccessful as true
$logoutNotSuccessful = true;

// Clean up the session (removing user data), if successful change $logoutNotSuccessful variable
if (session_destroy()) {$logoutNotSuccessful = false;}

?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles.css">
    <title>Logout</title>
<body>
<div class="delete-container text-center">
    <!--In this code, we introduced a variable $logoutNotSuccessful, which you should set to true when the logout is not
    successful. Then, in the HTML section, we use a conditional statement to display the appropriate message based on
    the value of $logoutNotSuccessful. If it's true, it displays the error message; otherwise, it displays the success message.-->
    <?php if ($logoutNotSuccessful): ?>
        <h1 class='display-6'>Logout was not successful. Please try again.</h1>
        <a href='../dash.php' class='btn btn-primary'>Back to Dash</a>
    <?php else: ?>
        <h1 class='display-6'>You have been successfully logged out!</h1>
        <a href='../index.php' class='btn btn-primary'>Home Page</a>
    <?php endif; ?>
</div>
</body>
</html>
