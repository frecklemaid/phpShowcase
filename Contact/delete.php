<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if(isset($_GET['id'])) {
    // Validate and sanitize the contact ID
    $contact_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($contact_id === false) {
        // Invalid or missing contact ID, redirect to Contact/index.php
        header("Location: ../Contact/index.php");
        exit();
    }

    // Fetch project details for confirmation
    $sql = "SELECT * FROM contact WHERE id=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$contact_id]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    //Fetch Clients for Options List
    $clients = [];
    $clientQuery = "SELECT id, fname, lname FROM clients";
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->execute();
    $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($contact) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle the form submission to delete a project
            $id = $_POST['id'];

            // Perform the database delete
            try {
                $sql = "DELETE FROM contact WHERE id=?";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([$id]);

                // Check the number of affected rows to determine if delete was successful
                if ($stmt->rowCount() > 0) {
                    // Redirect to the project list page after deleting the contact
                    header("Location: ../Contact/index.php");
                    exit();
                } else {
                    echo "The contact with ID $id was not found or could not be deleted.";
                }
            } catch (PDOException $e) {
                echo "There was a problem deleting the contact from the database: " . $e->getMessage();
                displayPDOError($e);
            }
        }
    } else {
        // Contact not found, redirect to Contact/index.php
        header("Location: ../Contact/index.php");
        exit();
    }
} else {
    // If there is no 'id' parameter in the URL, redirect to Contact/index.php
    header("Location: ../Contact/index.php");
    exit();
}
?>

<!-- Delete Project Confirmation (HTML) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Delete Project</title>
</head>
<body>
<div class="delete-container text-center">
    <header>
        <h1>Delete Contact</h1>
    </header>
    <?php if ($contact): ?>
        <h4>Are you sure you want to delete the contact "<?php echo htmlspecialchars($contact['fname']); ?>"?</h4>
        <form method="POST" action="">
            <!--Hidden Input of Selected Project To Send to Delete PHP Execution-->
            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
            <button type="submit" class="btn btn-danger delete-buttons">Delete</button>
            <!--if the user cancels, go back to db list-->
            <a href="../Contact/index.php" class="btn btn-success delete-buttons">Cancel</a>
        </form>
    <?php else: ?>
        <p>Contact not found.</p>
        <a href="../Contact/index.php" class="btn btn-success delete-buttons">Back to Contact List</a>
    <?php endif; ?>
</div>
</body>
</html>
