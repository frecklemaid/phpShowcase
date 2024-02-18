<!--View Organisation in Detail Page-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once("../connection.php");
require_once("../Auth/authenticate.php");

global $dbh;

// Check if a valid project ID is provided in the URL
if (isset($_GET['id'])) {
    $organisation_id = $_GET['id'];

    // Fetch organisation and clients
    $organisationQuery = "SELECT o.id, o.name, o.website, o.description, o.tech, o.industry, o.services, o.field, GROUP_CONCAT(' ', c.fname, ' ', c.lname) AS clients
                        FROM organisations o
                        LEFT JOIN clients_organisations co ON o.id = co.organisation_id
                        LEFT JOIN clients c ON co.client_id = c.id
                        WHERE o.id = :organisation_id
                        GROUP BY o.id
                        ORDER BY o.id";
    $orgStmt = $dbh->prepare($organisationQuery);
    $orgStmt->bindParam(':organisation_id', $organisation_id, PDO::PARAM_INT);
    $orgStmt->execute();
    $org = $orgStmt->fetch(PDO::FETCH_ASSOC);


    // Check if the project exists
    if (!$org) {
        header("Location: ../Organisations/index.php");
        exit(); // Add exit() to terminate the script
    }

} else {
    // If there is no project ID in the URL, redirect to the projects index
    header("Location: ../Projects/index.php");
    exit(); // Add exit() to terminate the script
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title><?php echo $org['name']; ?></title>
</head>
<body>
<div class="delete-container mt-5">
    <header class="text-center">
    </header>
    <div class="info">
        <!--First Column-->
        <div class="row justify-content-center">
            <div class="col align-self-center">
                <h1 class="display-3 text-center mb-3" id="pink-title"><?php echo $org['name']; ?></h1>
            </div>
        </div>
        <div class="info-table">

        <div class="row justify-content-center">
            <div class="col-md-6">
                <h4>Client(s)</h4>
                <p><?= $org['clients'] ?></p>
            </div>
            <div class="col-md-6">
                <h4>Technology</h4>
                <p><?php echo $org['tech']; ?></p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h4>Website</h4>
                <p><?= $org['website'] ?></p>
            </div>
            <div class="col-md-6">
                <h4>Industry</h4>
                <p><?php echo $org['industry']; ?></p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h4>Description</h4>
                <p><?php echo $org['description']; ?></p>
            </div>
            <div class="col-md-6">
                <h4>Services</h4>
                <p><?php echo $org['services']; ?></p>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-6">
                <h4>Field</h4>
                <p><?php echo $org['field']; ?></p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="view-buttons-container col align-self-center">
                <a href="../Organisations/update.php?id=<?= $org['id'] ?>" class="btn btn-primary view-buttons">Edit</a>
                <a href="../Organisations/index.php" class="btn btn-primary view-buttons">Back</a>
            </div>
        </div>
        </div>
    </div>

</div>
</body>
</html>
