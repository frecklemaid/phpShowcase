<!--Contact Modification Function-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

// Check if 'id' parameter is provided in the URL
if (isset($_GET['id'])) {
    $contact_id = $_GET['id'];

    // Fetch the contact record based on the 'id'
    $contactQuery = "SELECT * FROM contact WHERE id = :contact_id";
    $contactStmt = $dbh->prepare($contactQuery);
    $contactStmt->bindParam(':contact_id', $contact_id, PDO::PARAM_INT); // Bind the value
    $contactStmt->execute();
    $contact = $contactStmt->fetch(PDO::FETCH_ASSOC);

    //Check that the contact is a valid entry
    if (!$contact) {
        header("Location: " . dirname(APP_URL_PATH) . DIRECTORY_SEPARATOR . 'Contact' . DIRECTORY_SEPARATOR . 'index.php');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // See if checkbox has been selected
        $new_replied = isset($_POST['replied']) ? 1 : 0;

        // Retrieve any changes here
        $new_client_id = ($_POST['client_id'] !== "") ? $_POST['client_id'] : null;
        $new_message = ($_POST['message'] !== "") ? $_POST['message'] : null;
            //these should not be null as they are prefilled, required attributes
            $new_fname = $_POST['fname'];
            $new_lname = $_POST['lname'];
            $new_email = $_POST['email'];
            $new_phone = $_POST['phone'];

        //Update the database
        try {
            $update_sql = "UPDATE contact SET client_id=?, fname = ?, lname = ?, email = ?, phone=?, message=?, replied=? WHERE id = ?";
            $update_stmt = $dbh->prepare($update_sql);
            $update_stmt->execute([$new_client_id, $new_fname, $new_lname, $new_email, $new_phone, $new_message, $new_replied, $contact_id]);
        }
        catch (PDOException $e) {
            displayPDOError($e);
        }
        // Redirect to the contact list after updating
        header("Location: index.php");
        exit();
    }
    else {
        $clients = [];
        $clientQuery = "SELECT id, fname, lname FROM clients";
        $clientStmt = $dbh->prepare($clientQuery);
        $clientStmt->execute();
        $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // If there is no 'id' parameter in the URL, redirect to Contact/index.php
    header("Location: " . dirname(APP_URL_PATH) . DIRECTORY_SEPARATOR . 'Contact' . DIRECTORY_SEPARATOR . 'index.php');
    exit();
}
?>

<!-- HTML form for updating contact information -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Update Contact</title>
</head>
<body>
<div class="form-container">
    <h1>Update Contact</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="client_id">Client ID:</label>
            <select name="client_id" id="client_id" class="form-control">
                <!-- Display each client as an option for the project -->
                <option value="">No client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['id']; ?>" <?php echo (isset($current_client) && $client['id'] == $current_client) ? 'selected' : ''; ?>>
                        <?php echo $client['fname'] . ' ' . $client['lname']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fname">First Name</label>
            <!--Name must include letters only - no special characters-->
            <input type="text" name="fname" id="fname" class="form-control" title="Names must include only letters - no special characters" placeholder="John" maxlength="50" pattern="[A-Za-z]+" value="<?= $contact['fname'] ?>" required>
        </div>
        <div class="form-group">
            <label for="lname">Surname</label>
            <!--Name must include letters only - no special characters-->
            <input type="text" name="lname" id="lname" class="form-control" title="Names must include only letters - no special characters" placeholder="Doe" maxlength="50" pattern="[A-Za-z]+" value="<?= $contact['lname'] ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <!--Standard Email Addresses may only be inputted-->
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address" maxlength="254" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="<?= $contact['email'] ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone (Mobile):</label>
            <!--Phone Input Must Follow the Pattern of an Australian Mobile-->
            <input type="tel" name="phone" id="phone" class="form-control" title="Phone input must match an Australian mobile" placeholder="Enter Australian mobile" maxlength="12" pattern="^(\+?\(61\)|\(\+?61\)|\+?61|\(0[1-9]\)|0[1-9])?( ?-?[0-9]){7,9}$" value="<?= $contact['phone'] ?>" required>
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea name="message" id="message" class="form-control" placeholder="Enter client message" rows="5"><?= $contact['message'] ?></textarea>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="replied" name="replied" value="<?= $contact['replied']?>">
            <label class="form-check-label" for="replied">Replied</label>
        </div>

        <button type="submit" class="btn btn-primary buttons">Update Contact</button>
        <a href="../Contact/index.php" class="btn btn-success buttons">Cancel</a>
    </form>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
