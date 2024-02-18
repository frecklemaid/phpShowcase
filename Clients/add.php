<!-- Add New Customer -->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

// Retrieve all organisations
$organisations = [];
$orgQuery = "SELECT id, name FROM organisations";
$orgStmt = $dbh->prepare($orgQuery);
$orgStmt->execute();
$organisations = $orgStmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve all contacts
$contacts = [];
$contactQuery = "SELECT id, fname, lname, email, phone FROM contact"; // Include email and phone
$contactStmt = $dbh->prepare($contactQuery);
$contactStmt->execute();
$contacts = $contactStmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve contact from ID sent by convert contact-> client button
$thisContact = null;

// Check if an ID is provided in the URL, if not $thisContact will remain null
if (isset($_GET['id'])) {
    $contactId = $_GET['id'];
    // Iterate through the contacts array to find the matching contact
    foreach ($contacts as $contact) {
        if ($contact['id'] == $contactId) {
            // Found the contact with the matching ID
            $thisContact = $contact;
            $contactFound = true;
            break; // Stop the loop since we found the contact
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Generate a unique identifier (timestamp + random number)
            $uniqueID = time() . '_' . mt_rand(1000, 9999);

            // Get the original file extension (assuming the file has an extension)
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            // Create a new filename with the unique ID and the original extension
            $newFileName = $uniqueID . '.' . $extension;

            // Construct the destination path with the unique filename
            $destination = APP_FOLDER_PATH . DIRECTORY_SEPARATOR . 'clients_profiles' . DIRECTORY_SEPARATOR . $newFileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    // Update the record based on the form received
                    $clientInsertSql = "INSERT INTO clients (photo, fname, lname, email, phone, suburb, address, recruitment) 
                    VALUES (:photo, :fname, :lname, :email, :phone, :suburb, :address, :recruitment)";
                    $clientStmt = $dbh->prepare($clientInsertSql);


                    // Execute the query
                    $clientStmt->execute([
                        'photo' => $newFileName,
                        'fname' => $_POST['fname'],
                        'lname' => $_POST['lname'],
                        'email' => $_POST['email'],
                        'phone' => $_POST['phone'],
                        'suburb' => $_POST['suburb'],
                        'address' => $_POST['address'],
                        'recruitment' => $_POST['recruitment'] ?? null, //Allow null
                    ]);


                //Retrieve selected organisations
                $selectedOrganisations = isset($_POST['organisations']) ? $_POST['organisations'] : [];

                // Get the ID of the newly inserted client
                $client_id = $dbh->lastInsertId();

                // Insert selected organizations into the 'clients_organisations' table
                $orgInsertSql = "INSERT INTO clients_organisations (client_id, organisation_id) VALUES (:client_id, :organisation_id)";
                $orgStmt = $dbh->prepare($orgInsertSql);

                // Insert each selected organization for the client
                foreach ($selectedOrganisations as $organisation) {
                    $orgStmt->execute([
                        'client_id' => $client_id,
                        'organisation_id' => $organisation,
                    ]);
                }

                // Redirect to the client list page
                header('Location: index.php');
                exit();
            } else {
                throw new Exception("<div class='alert alert-danger'>Cannot store file. See warning for more information.</div>");
            }
        } else {
            throw new Exception("<div class='alert alert-danger'>Uploaded file cannot be processed. Error code: " . $_FILES['image']['error'] . "</div>");
        }
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }

}
?>
<!-- Form to Add New Client to DB -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
    <title>Add New Client</title>
</head>
<body>
<div class="form-container mt-5">
    <header>
        <h1 class="mb-4">Add New Client</h1>
    </header>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="image" class="form-label">Photo (Image Only):</label>
            <input type="file" name="image" id="image" accept="image/*" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="contact" class="form-label">Contact:</label>
            <select name="contact" id="contact" class="form-select">
                <option value="">No contact</option>
                <?php foreach ($contacts as $contact): ?>
                    <option value="<?php echo $contact['id']; ?>"
                            data-fname="<?php echo $contact['fname']; ?>"
                            data-lname="<?php echo $contact['lname']; ?>"
                            data-email="<?php echo $contact['email']; ?>"
                            data-phone="<?php echo $contact['phone']; ?>"
                        <?php if ($thisContact !== null && $contact['id'] == $thisContact['id']): ?>
                            selected="selected"
                        <?php endif; ?>>
                        <?php echo $contact['fname'] . ' ' . $contact['lname']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="fname" class="form-label">First Name:</label>
            <input type="text" name="fname" id="fname" class="form-control"
                   title="Names must include only letters - no special characters" placeholder="John" maxlength="50"
                   pattern="[A-Za-z]+" required>
        </div>
        <div class="mb-3">
            <label for="lname" class="form-label">Last Name:</label>
            <input type="text" name="lname" id="lname" class="form-control"
                   title="Names must include only letters - no special characters" placeholder="Doe" maxlength="50"
                   pattern="[A-Za-z]+" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address"
                   maxlength="254" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone:</label>
            <input type="tel" name="phone" id="phone" class="form-control"
                   title="Phone input must match an Australian mobile" placeholder="Enter Australian mobile"
                   maxlength="12" pattern="^(\+?\(61\)|\(\+?61\)|\+?61|\(0[1-9]\)|0[1-9])?( ?-?[0-9]){7,9}$" required>
        </div>
        <div class="mb-3">
            <label for="suburb" class="form-label">Suburb:</label>
            <input type="text" class="form-control" id="suburb" name="suburb" maxlength="250" placeholder="Enter suburb"
                   title="Entry must be less than 250 characters" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address"
                   title="Entry must be less than 250 characters" maxlength="250" required>
        </div>
        <div class="mb-3">
            <label for="recruitment" class="form-label">Recruitment:</label>
            <input type="text" class="form-control" id="recruitment" name="recruitment" maxlength="100"
                   title="Entry must be less than 100 characters" placeholder="Enter recruitment method">
        </div>
        <div class="mb-3">
            <label for="organisations" class="form-label">Organisations:</label>
            <select class="form-select" id="organisations" name="organisations[]" multiple data-live-search="true">
                <option value="">No organisation</option>
                <!-- Dynamically load in organisations as multiple select options -->
                <?php foreach ($organisations as $organisation): ?>
                    <option value="<?php echo $organisation['id']; ?>">
                        <?php echo $organisation['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="add" id="addClientButton">Add Client</button>
        <a href="../Clients/index.php" class="btn btn-success">Cancel</a>
        <script>
            $(document).ready(function () {
                // Function to populate form fields based on selected contact
                function populateFields() {
                    const selectedContact = $('#contact :selected');
                    $('#fname').val(selectedContact.data('fname'));
                    $('#lname').val(selectedContact.data('lname'));
                    $('#email').val(selectedContact.data('email'));
                    $('#phone').val(selectedContact.data('phone'));
                }

                // Populate fields when the page loads
                populateFields();

                // Listen for changes to the select element
                $('#contact').change(populateFields);
            });
        </script>
        <script>
            document.getElementById('image').onchange = (event) => {
                // Check if JS is allowed to do file manipulation
                if (typeof FileReader !== "undefined") {
                    // Check if a file has been selected
                    if (event.target.files.length === 1) {
                        // Get file type and size
                        let fileSize = event.target.files[0].size;

                        if (fileSize > 2000000)
                            // Check if the file ls bigger than 2MB
                            event.target.setCustomValidity("File size must not exceed 2MB");
                        else
                            // Otherwise clear the invalid message from the form control
                            event.target.setCustomValidity("");
                    } else {
                        event.target.setCustomValidity("A file must be provided");
                    }
                }
            }

        </script>
    </form>
</div>
</body>
</html>
