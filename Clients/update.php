<!--Client Modification Form-->
<?php
global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Check if the client ID is provided in the query string
if (isset($_GET['id'])) {
    $client_id = $_GET['id'];

    // Fetch the client record based on the 'id'
    $clientQuery = "SELECT * FROM clients
            WHERE id = :client_id";
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $clientStmt->execute();
    $client = $clientStmt->fetch(PDO::FETCH_ASSOC);
    if (!$client) {
        // If the client with the id is no longer in the DB, redirect
        header("Location: ../Clients/index.php");
        exit();
    }

    //Fetch all organisations for multiple select
    $organisations = [];
    $orgQuery = "SELECT id, name FROM organisations";
    $orgStmt = $dbh->prepare($orgQuery);
    $orgStmt->execute();
    $organisations = $orgStmt->fetchAll(PDO::FETCH_ASSOC);

    //Create list of existing organisations client is associated with for preselection
    $clientsOrganisations = [];
    $coQuery = "SELECT organisation_id FROM clients_organisations WHERE client_id = :clientid";
    $coStmt = $dbh->prepare($coQuery);
    $coStmt->bindParam(':clientid', $client_id, PDO::PARAM_INT); // Use 'PDO::PARAM_INT' instead of 'type:'
    $coStmt->execute();
    // Fetch and store the results in the $clientsOrganisations array
    while ($row = $coStmt->fetch(PDO::FETCH_ASSOC)) {
        $clientsOrganisations[] = $row['organisation_id'];
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve changes other than image here
        //The following should not be null as they are prefilled and required attributes by default
        try {
            $new_fname = $_POST['fname'];
            $new_lname = $_POST['lname'];
            $new_email = $_POST['email'];
            $new_phone = $_POST['phone'];
            $new_suburb = $_POST['suburb'];
            $new_address = $_POST['address'];
        } catch (Exception $e) {
            //
            echo "Error: " . $e->getMessage();
            echo "One of the required attributes was not filled in";
        }
        //These may be null
        $new_recruitment = isset($_POST['recruitment']) ? $_POST['recruitment'] : null;
        $new_organisations = isset($_POST['organisations']) ? $_POST['organisations'] : null;

        //If there is no new file (photo) uploaded
        if (!isset($_FILES['image']) || $_FILES['image']['error'] == 4) {
            try {
                $updateQuery = "UPDATE clients SET fname = :fname, lname = :lname, email = :email, phone = :phone, 
                            suburb = :suburb, address = :address, recruitment = :recruitment WHERE id = :client_id";
                $updateStmt = $dbh->prepare($updateQuery);

                // Define an associative array with placeholders as keys and values
                $params = [
                    ':fname' => $new_fname,
                    ':lname' => $new_lname,
                    ':email' => $new_email,
                    ':phone' => $new_phone,
                    ':suburb' => $new_suburb,
                    ':address' => $new_address,
                    ':recruitment' => $new_recruitment,
                    ':client_id' => $client_id,
                ];

                // Execute the statement with the associative array
                $updateStmt->execute($params);

                // Handle updating organizations here
                $deleteOrgsSql = "DELETE FROM clients_organisations WHERE client_id = :client_id";
                $deleteOrgsStmt = $dbh->prepare($deleteOrgsSql);
                $deleteOrgsStmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
                $deleteOrgsStmt->execute();

                $orgInsertSql = "INSERT INTO clients_organisations (client_id, organisation_id) VALUES (:client_id, :organisation_id)";
                $orgStmt = $dbh->prepare($orgInsertSql);

                // Insert each selected organization for the client
                foreach ($new_organisations as $orgId) {
                    $orgStmt->execute([
                        'client_id' => $client_id,
                        'organisation_id' => $orgId,
                    ]);
                }
                // Redirect to the client list after updating
                header("Location: ../Clients/index.php");
                exit();
            }catch (PDOException $e) {
                displayPDOError($e);
            }
        }
        //If a new profile photo has been uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            try {
                // Delete old file first
                $filePath = APP_FOLDER_PATH . DIRECTORY_SEPARATOR . 'clients_profiles' . DIRECTORY_SEPARATOR . $client['photo'];
                try {
                    if (unlink($filePath) === false) {
                        throw new Exception("Failed to delete file from filesystem: " . $filePath);
                    }
                } catch (Exception $e) {
                    // Handle the exception, e.g., log the error or display an error message
                    echo "Error: " . $e->getMessage();
                }


                // Set the file destination and move it from its temporary location
                // Generate a unique identifier (timestamp + random number)
                $uniqueID = time() . '_' . mt_rand(1000, 9999);

                // Get the original file extension (assuming the file has an extension)
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

                // Create a new filename with the unique ID and the original extension
                $newFileName = $uniqueID . '.' . $extension;

                // Construct the destination path with the unique filename
                $destination = APP_FOLDER_PATH . DIRECTORY_SEPARATOR . 'clients_profiles' . DIRECTORY_SEPARATOR . $newFileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $updateQuery = "UPDATE clients SET photo = :photo, fname = :fname, lname = :lname, email = :email, phone = :phone, 
                            suburb = :suburb, address = :address, recruitment = :recruitment WHERE id = :client_id";
                    $updateStmt = $dbh->prepare($updateQuery);

                    // Define an associative array with placeholders as keys and values
                    $params = [
                        ':photo' => $newFileName,
                        ':fname' => $new_fname,
                        ':lname' => $new_lname,
                        ':email' => $new_email,
                        ':phone' => $new_phone,
                        ':suburb' => $new_suburb,
                        ':address' => $new_address,
                        ':recruitment' => $new_recruitment,
                        ':client_id' => $client_id,
                    ];

                    // Execute the statement with the associative array
                    $updateStmt->execute($params);

                    // Handle updating organizations here
                    $deleteOrgsSql = "DELETE FROM clients_organisations WHERE client_id = :client_id";
                    $deleteOrgsStmt = $dbh->prepare($deleteOrgsSql);
                    $deleteOrgsStmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
                    $deleteOrgsStmt->execute();

                    $orgInsertSql = "INSERT INTO clients_organisations (client_id, organisation_id) VALUES (:client_id, :organisation_id)";
                    $orgStmt = $dbh->prepare($orgInsertSql);

                    // Insert each selected organization for the client
                    foreach ($new_organisations as $orgId) {
                        $orgStmt->execute([
                            'client_id' => $client_id,
                            'organisation_id' => $orgId,
                        ]);
                    }

                    // Redirect to the client list after updating
                    header("Location: ../Clients/index.php");
                    exit();
                } else {
                    throw new Exception("Cannot store file. See warning for more information. ");

                }
            } catch (PDOException $e) {
                displayPDOError($e);
            }
    }
}
}
    else {
    // If there is no 'id' parameter in the URL, redirect to Client/index.php
    header("Location: ../Clients/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles.css">
    <title>Edit Client</title>
</head>
<body>
<div class="form-container mt-5">
    <h1>Edit Client</h1>
    <!--Client Attribute Update Form - displays existing, unchanged data by default-->
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="image" class="form-label">Photo (Image Only):</label><br>
            <!-- Display Current Client Photo -->
            <img src="<?= APP_URL_PATH ?>/clients_profiles/<?= rawurlencode($client['photo']) ?>" alt="Client Photo" id="profile-photo" class="img-fluid mb-1"><br>
            <!--Only Accept Image Input Types-->
            <input type="file" name="image" id="image" accept="image/*" class="form-control">
        </div>
        <input type="hidden" name="client_id" value="<?= $client_id ?>">
        <div class="mb-3">
            <label for="fname">First Name:</label>
            <input type="text" name="fname" id="fname" class="form-control"
                   title="Names must include only letters - no special characters" placeholder="John" maxlength="50"
                   pattern="[A-Za-z]+" value="<?= $client['fname'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="lname">Last Name:</label>
            <input type="text" name="lname" id="lname" class="form-control"
                   title="Names must include only letters - no special characters" placeholder="John" maxlength="50"
                   pattern="[A-Za-z]+" value="<?= $client['lname'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address"
                   maxlength="254" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="<?= $client['email'] ?>"
                   required>
        </div>
        <div class="mb-3">
            <label for="phone">Phone:</label>
            <input type="text" class="form-control" id="phone" name="phone"
                   title="Phone input must match an Australian mobile" placeholder="Enter Australian mobile"
                   maxlength="12" pattern="^(\+?\(61\)|\(\+?61\)|\+?61|\(0[1-9]\)|0[1-9])?( ?-?[0-9]){7,9}$"
                   value="<?= $client['phone'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="suburb">Suburb:</label>
            <input type="text" class="form-control" id="suburb" name="suburb" placeholder="Enter Address"
                   title="Entry must be less than 250 characters" maxlength="250" value="<?= $client['suburb'] ?>"
                   required>
        </div>
        <div class="mb-3">
            <label for="address">Address:</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address"
                   title="Entry must be less than 250 characters" maxlength="250" value="<?= $client['address'] ?>"
                   required>
        </div>
        <div class="mb-3">
            <label for="recruitment">Recruitment:</label>
            <input type="text" class="form-control" id="recruitment" name="recruitment"
                   maxlength="100" title="Entry must be less than 100 characters" placeholder="Enter recruitment method"
                   value="<?= $client['recruitment'] ?>">
        </div>
        <div class="mb-3">
            <label for="organisations">Organisations:</label>
            <select class="form-control selectpicker" id="organisations" name="organisations[]" multiple data-live-search="true">
                <option value="">No organisation</option>
                <!-- Dynamically load in organisations as multiple select options -->
                <?php foreach ($organisations as $organisation): ?>
                    <?php
                    // Check if the current organization is in the client's selected organizations
                    $isSelected = in_array($organisation['id'], $clientsOrganisations);
                    ?>
                    <option value="<?php echo $organisation['id']; ?>" <?php if ($isSelected) echo 'selected'; ?>>
                        <?php echo $organisation['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary buttons" name="update">Update Client</button>
        <a href="../Clients/index.php" class="btn btn-success buttons">Cancel</a>
    </form>
</div>
<script>
    document.getElementById('image').onchange = (event) => {
        // Check if JS is allowed to do file manipulation
        if (typeof FileReader !== "undefined") {
            // Get file type and size
            let fileSize = event.target.files[0].size;

            if (fileSize > 2000000)
                // Check if the file ls bigger than 2MB
                event.target.setCustomValidity("File size must not exceed 2MB");
            else
                // Otherwise clear the invalid message from the form control
                event.target.setCustomValidity("");
        }
    }
</script>
</body>
</html>
