<!--Delete Client-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once("../connection.php");
require_once("../Auth/authenticate.php");

global $dbh;

if (isset($_GET['id'])) {
    $directory = '../clients_profiles';

    //Fetch the client information
    $client_id = $_GET['id'];
    $clientQuery = "SELECT * FROM `clients` WHERE id=:client_id";
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $clientStmt->execute();
    $client = $clientStmt->fetch(PDO::FETCH_ASSOC);
    if (!$client) {
        // If the client with the id is no longer in the DB, redirect
        header("Location: ../Clients/index.php");
        exit();
    }

    //Fetch all contacts where client is assigned
    $contacts = [];
    $contactQuery = "SELECT id FROM `contact` WHERE client_id = :client_id";
    $contactStmt = $dbh->prepare($contactQuery);
    $contactStmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $contactStmt->execute();
    while ($row = $contactStmt->fetch(PDO::FETCH_ASSOC)) {
        $contacts[] = $row['id'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];

        if ($client['photo'] != null) {
            $file = $client['photo'];
            $fileWithPath = $directory . '/' . $file;

            if (file_exists($fileWithPath)) {
                if (unlink($fileWithPath)) {
                    try {
                        //Handle deletion of client from clients_organisations
                        $deleteOrgsQuery = "DELETE FROM clients_organisations WHERE client_id = :client_id";
                        $deleteOrgsStmt = $dbh->prepare($deleteOrgsQuery);
                        $deleteOrgsStmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
                        $deleteOrgsStmt->execute();

                       //Handle deletion of client from contact
                        foreach ($contacts as $contact) {
                            $updateContactQuery = "UPDATE contact SET client_id = NULL WHERE id = :contact_id";
                            $updateContactStmt = $dbh->prepare($updateContactQuery);
                            $updateContactStmt->bindParam(':contact_id', $contact, PDO::PARAM_INT);
                            $updateContactStmt->execute();
                        }

                        //Finally, delete the client record
                        $deleteQuery = "DELETE FROM `clients` WHERE id=:client_id";
                        $deleteStmt = $dbh->prepare($deleteQuery);
                        $deleteStmt->bindParam(':client_id', $id, PDO::PARAM_INT);
                        $deleteStmt->execute();

                        // Redirect the user back to the previous page
                        header("Location: ../Clients/index.php");
                        exit(); // Add exit() to terminate the script
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage(); // Display an error message
                    }
                } else {
                    throw new Exception("Error deleting profile photo");
                }
            }
            else {
                throw new Exception("The profile photo at recorded path does not exist.");
            }
        }
        else {
            throw new Exception("Client does not have required profile photo");
        }
    }
} else {
    // If there is no 'id' parameter in the URL, redirect to Clients/index.php
    header("Location: ../Clients/index.php");
    exit(); // Add exit() to terminate the script
}
?>


<!-- Delete Clients Confirmation (HTML) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Delete Client</title>
</head>
<body>
<div class="delete-container text-center">
    <header>
        <h1>Delete Client</h1>
    </header>
        <h4>Are you sure you want to delete the client "<?php echo ($client['fname'] . ' ' . $client['lname']); ?>"?</h4>
        <h5 class="text-danger">Warning: This will remove the client from the organisation and contact records it is tied to.</h5>
        <form method="POST" action="">
            <!--Hidden Input of Selected Client To Send to Delete PHP Execution-->
            <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
            <button type="submit" class="btn btn-danger buttons">Delete</button>
            <!--if the user cancels, go back to db list (in the "Client" directory)-->
            <a href="../Clients/index.php" class="btn btn-success buttons">Cancel</a>
        </form>
</div>
</body>
</html>
