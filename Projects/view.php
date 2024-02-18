<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require_once("../connection.php");
require_once("../Auth/authenticate.php");

global $dbh;

// Check if a valid project ID is provided in the URL
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];

    // Fetch project details for editing
    $projectQuery = "SELECT * FROM projects WHERE id = :project_id";
    $projectStmt = $dbh->prepare($projectQuery);
    $projectStmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $projectStmt->execute();
    $project = $projectStmt->fetch(PDO::FETCH_ASSOC);

    // Check if the project exists
    if (!$project) {
        header("Location: ../Projects/index.php");
        exit(); // Add exit() to terminate the script
    }

    // Retrieve Client Information
    $client_id = $project['client_id'];
    $clientQuery = "SELECT fname, lname FROM clients WHERE id = :client_id";
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $clientStmt->execute();
    $client = $clientStmt->fetch(PDO::FETCH_ASSOC);

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
    <title><?php echo $project['name']; ?></title>
</head>
<body>
<div class="delete-container mt-5">
    <div class="info">
        <!-- First Column -->
        <div class="row">
            <div class="col text-center">
                <h1 class="display-3" id="pink-title"><?= $project['name']; ?></h1>
                <h2 class="mb-3"><?= $project['semester_year']; ?></h2>
            </div>
        </div>
        <div class="info-table">
        <div class="row">
            <div class="col-md-6">
                <h4>Client</h4>
                <p><?= $client['fname'] . ' ' . $client['lname']; ?></p>
            </div>
            <div class="col-md-6">
                <h4>Strengths</h4>
                <p><?= $project['strengths']; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>Description</h4>
                <p><?= $project['description']; ?></p>
            </div>
            <div class="col-md-6">
                <h4>Weaknesses</h4>
                <p><?= $project['weaknesses']; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>Proposal <i class="fa-solid fa-paperclip text-success"></i></h4>
                <?php
                // If there is a proposal uploaded, display a link to it. Otherwise, display 'no file'.
                if ($project['proposal_path'] != null) {
                    echo '<a href="' . APP_URL_PATH . '/projects_proposals/' . rawurlencode($project['proposal_path']) . '" target="_blank">View</a>';
                } else {
                    echo '<p>No proposal attached.</p>';
                }
                ?>
            </div>
        </div>
        </div>
        <div class="row justify-content-center">
            <div class="view-buttons-container col align-self-center">
                <a href="../Projects/update.php?id=<?= $project['id'] ?>" class="btn btn-primary view-buttons">Edit</a>
                <a href="../Projects/index.php" class="btn btn-primary view-buttons">Back</a>
            </div>
        </div>
    </div>


</div>
</body>
</html>
