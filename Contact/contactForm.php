<!--Customer-Facing Contact Form-->
<!--Registers contact in database and sends to Nathan's email-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");

//Initialise Email Function
function sendEmail($to, $subject, $message, $headers) {
    return mail($to, $subject, $message, $headers);
}

$sent = null;
// If the user has completed the form:
if ($_SERVER['REQUEST_METHOD'] == 'POST'):
    // Add new record based on the form received
    $sql = "INSERT INTO contact (client_id, fname, lname, email, phone, message, replied)
            VALUES (:client_id, :fname, :lname, :email, :phone, :message, :replied)";
    $stmt = $dbh->prepare($sql);
    // Bind parameters

    try {
            $stmt->execute([
                'client_id' => null,
                'fname' => $_POST['fname'],
                'lname' => $_POST['lname'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'message' =>$_POST['message'],
                'replied' => null // Set the replied field based on checkbox
            ]);

        //Email Notification Send
        $to = 'nathan.recruiter@example.com';
        $subject = 'New Contact Form Submission';
        $message = "Name: {$_POST['fname']} {$_POST['lname']}\n";
        $message .= "Email: {$_POST['email']}\n";
        $message .= "Phone: {$_POST['phone']}\n";
        $message .= "Message: {$_POST['message']}\n";
        $headers = "From: {$_POST['email']}";

        if (!sendEmail($to, $subject, $message, $headers)) {
            //If the email was not sent, append error message.
            echo '<div class="alert alert-danger"><p>There has been an error sending your message by email. Please try again later.</p></div>';
        }
        else {
            //If the email was sent, state that it has been successfully sent.
            echo '<div class="alert alert-success"><p>Your message has been sent.</p></div>';
        }

        exit();
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger"><p>There was a problem adding your message to the database.</p></div>';
        displayPDOError($e);
    }
endif;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nathan Altman</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body class="frontend-body">
<div id="box">
    <header>
        <h1 class="pink-title display-1" style="color: #f86e87; text-align: center;">Contact Us</h1>
    </header>
    <form class="frontend-forms" action="" method="POST">
        <div class="form-group row mb-3">
            <label for="fname">First Name</label>
            <!--Name must include letters only - no special characters-->
            <input type="text" name="fname" class="form-control" id="fname" title="Names must include only letters - no special characters" placeholder="John" maxlength="50" pattern="[A-Za-z]+" required>
        </div>
        <div class="form-group row mb-3">
            <label for="lname">Surame</label>
            <!--Name must include letters only - no special characters-->
            <input type="text" name="lname" class="form-control" id="lname" title="Names must include only letters - no special characters" placeholder="Doe" maxlength="50" pattern="[A-Za-z]+" required>
        </div>
        <div class="form-group row mb-3">
            <label for="email">Email address</label>
            <!--Standard Email Addresses may only be inputted-->
            <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" maxlength="254" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" required>
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group row mb-3">
            <label for="phone">Phone (Mobile):</label>
            <!--Phone Input Must Follow the Pattern of an Australian Mobile-->
            <input type="tel" name="phone" class="form-control" id="phone" title="Phone input must match an Australian mobile" placeholder="Enter Australian mobile" maxlength="12" pattern="^(\+?\(61\)|\(\+?61\)|\+?61|\(0[1-9]\)|0[1-9])?( ?-?[0-9]){7,9}$" required>
        </div>
        <div class="form-group row mb-3">
            <label for="message">What do we need to know?</label>
            <!--Message Input Has An 8000 Character Maximum-->
            <textarea name="message" id="message" class="form-control" placeholder="Let us know what we can help you with" maxlength="8000" required></textarea>
        </div>
        <div class="form-group row mb-3">
            <button type="submit" class="btn btn-primary" id="send">Send</button>
        </div>
    </form>
    <?php

    ?>

</div>
<footer>
</footer>
</body>
</html>

