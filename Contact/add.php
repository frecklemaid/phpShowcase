<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Prepare the database insert query
    $sql = "INSERT INTO contact (client_id, fname, lname, email, phone, message, replied)
        VALUES (:client_id, :fname, :lname, :email, :phone, :message, :replied)";
    $stmt = $dbh->prepare($sql);

    try {
        // Determine if the checkbox is checked and set replied accordingly
        $replied = isset($_POST['replied']) ? 1 : 0;

        // Execute the query
        $stmt->execute([
            'client_id' => ($_POST['client_id'] !== "") ? $_POST['client_id'] : null,
            'fname' => $_POST['fname'],
            'lname' => $_POST['lname'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'message' => ($_POST['message'] !== "") ? $_POST['message'] : null,
            'replied' => $replied // Set the replied field based on checkbox
        ]);
        header('Location: index.php');
        exit(); // Add this exit to terminate the script after the redirect
    } catch (PDOException $e) {
        echo "There was a problem inserting the data into the database.";
        displayPDOError($e);
    }
} else {
    // If the form hasn't been submitted yet (i.e., when it loads), load clients from clients table as options
    $clients = [];
    $clientQuery = "SELECT id, fname, lname, email, phone FROM clients"; // Include email and phone
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->execute();
    $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);

    //Handle if client ID has been passed through in URL (ie. message is being created from an existing client)
    $thisClient = null;
            if (isset($_GET['id'])) {
            // If this form has been opened from the Contact table to convert client, retrieve contact
            foreach ($clients as $client) {
                if ($client['id'] == $_GET['id']) {
                    // Found the client with the matching ID
                    $thisClient = $client;
                    break; // Stop the loop since we found the client
                }
            }
        }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Create Contact</title>
</head>
<body>
<div class="form-container">
    <header>
        <h1>Create Contact</h1>
    </header>
    <form method="POST" action="">
        <div class="form-group">
            <label for="client_id">Client ID:</label>
            <select name="client_id" id="client_id" class="form-control">
                <!-- Display each client as an option for the Contact -->
                <option value="">No client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['id']; ?>"
                            data-fname="<?php echo $client['fname']; ?>"
                            data-lname="<?php echo $client['lname']; ?>"
                            data-email="<?php echo $client['email']; ?>"
                            data-phone="<?php echo $client['phone']; ?>"
                        <?php if ($thisClient !== null && $client['id'] == $thisClient['id']): ?>
                            selected="selected"
                        <?php endif; ?>>
                        <?php echo $client['fname'] . ' ' . $client['lname']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="fname">First Name:</label>
            <!--Name must include letters only - no special characters-->
            <input type="text" name="fname" id="fname" title="Names must include only letters - no special characters" class="form-control" maxlength="50" pattern="[A-Za-z]+" placeholder="John" required>
        </div>
        <div class="form-group">
            <label for="lname">Surname:</label>
            <!--Name must include letters only - no special characters-->
            <input type="text" class="form-control" id="lname" name="lname" title="Names must include only letters - no special characters" maxlength="50" pattern="[A-Za-z]+" placeholder="Doe" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <!--Standard Email Addresses may only be inputted-->
            <input type="email" class="form-control" id="email" name="email" maxlength="254" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" placeholder="Enter email address" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone (Mobile):</label>
            <!--Phone Input Must Follow the Pattern of an Australian Mobile-->
            <input type="text" class="form-control" id="phone" title="Phone input must match an Australian mobile" placeholder="Enter Australian mobile" name="phone" maxlength="12" pattern="^(\+?\(61\)|\(\+?61\)|\+?61|\(0[1-9]\)|0[1-9])?( ?-?[0-9]){7,9}$" required>
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <!--Message Input Has An 8000 Character Maximum-->
            <textarea name="message" id="message" class="form-control" placeholder="Enter contact message" maxlength="8000" rows="5"></textarea>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="replied" name="replied" value="1">
            <label class="form-check-label" for="replied">Replied</label>
        </div>
        <button type="submit" class="btn btn-primary buttons">Create Contact</button>
        <a href="../Contact/index.php" class="btn btn-success buttons">Cancel</a>

    </form>

    <script>
        $(document).ready(function () {
            // Function to populate form fields based on selected client
            function populateFields() {
                var selectedClient = $('#client_id').find(':selected');
                var fname = selectedClient.data('fname');
                var lname = selectedClient.data('lname');
                var email = selectedClient.data('email');
                var phone = selectedClient.data('phone');

                $('#fname').val(fname);
                $('#lname').val(lname);
                $('#email').val(email);
                $('#phone').val(phone);
            }

            // Populate fields when the page loads
            populateFields();

            // Listen for changes to the select element
            $('#client_id').change(function () {
                // Populate the form fields when a client is selected
                populateFields();
            });
        });
    </script>
</div>
</body>
</html>
