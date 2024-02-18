<!--Delete Organisation-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
//session_start();
global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if(isset($_GET['id'])) {
    // Fetch organisation details for confirmation
    $organisation_id = $_GET['id'];
    $orgQuery = "SELECT * FROM organisations WHERE id=?";
    $orgStmt = $dbh->prepare($orgQuery);
    $orgStmt->execute([$organisation_id]);
    $org = $orgStmt->fetch(PDO::FETCH_ASSOC);
    if (!$org) {
        //If the organisation isn't valid
        header("Location: ../Organisations/index.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle the form submission to delete a organisation
        $id = $_POST['id'];

        try {
            //First, delete records from clients_organisations
            $deleteClientsSql = "DELETE FROM clients_organisations WHERE organisation_id = :organisation_id";
            $deleteClientsStmt = $dbh->prepare($deleteClientsSql);
            $deleteClientsStmt->bindParam(':organisation_id', $organisation_id, PDO::PARAM_INT);
            $deleteClientsStmt->execute();

            // Second, perform the database delete for organisations
            $deleteQuery = "DELETE FROM organisations WHERE id=?";
            $deleteStmt = $dbh->prepare($deleteQuery);
            $deleteStmt->execute([$id]);
        } catch (PDOException $e){
            echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
        // Redirect to the organisation list page after deleting the organisation
        header("Location: ../Organisations/index.php");
        exit();
    }
} else {
    // If there is no 'id' parameter in the URL, redirect to Organisations/index.php
    header("Location: ../Organisations/index.php");
    exit();
}
?>


<!-- Delete Organisation Confirmation (HTML) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Delete Organisation</title>

</head>
<body>
<div class="delete-container text-center">
    <header>
        <h1>Delete Organisation</h1>
    </header>
    <h4>Are you sure you want to delete the organisation "<?php echo $org['name']; ?>"?</h4>
    <form method="POST" action="">
        <!--Hidden Input of Selected Organisation To Send to Delete PHP Execution-->
        <input type="hidden" name="id" value="<?php echo $org['id']; ?>">
        <button type="submit" class="btn btn-danger delete-buttons" value="Delete">Delete</button>
        <!--If the user cancels, go back to db list-->
        <a href="../Organisations/index.php" class="btn btn-success delete-buttons">Cancel</a>
    </form>
</div>
</body>
</html>
