<!--Delete Project-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if (isset($_GET['id'])) {
    $directory = '../projects_proposals';

    // Fetch project details for confirmation
    $project_id = $_GET['id'];
    $sql = "SELECT * FROM projects WHERE id=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$project) {
        Header("Location: ../Projects/index.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle the form submission to delete a project
        $id = $_POST['id'];

        if ($project['proposal_path'] != null) {
            $file = $project['proposal_path'];
            $fileWithPath = $directory . '/' . $file; // Full path to the file

            // Check if the file exists before attempting to delete it
            if (file_exists($fileWithPath)) {
                if (unlink($fileWithPath)) {
                    // Delete the record from the database
                    try {
                        $deleteQuery = "DELETE FROM `projects` WHERE id=?";
                        $deleteStmt = $dbh->prepare($deleteQuery);
                        $deleteStmt->execute([$id]);
                    }catch (PDOException $e) {
                        echo "Error: " . $e->getMessage(); // Display any database errors
                    }

                    // Redirect the user back to the previous page
                    header("Location: ../Projects/index.php");
                    exit();
                } else {
                    // Handle file deletion error here
                    echo "Error deleting the file.";
                }
            } }
        else {
                // The file does not exist, proceed with the database deletion
                try {
                    $deleteQuery = "DELETE FROM `projects` WHERE id=?";
                    $deleteStmt = $dbh->prepare($deleteQuery);
                    $deleteStmt->execute([$id]);
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage(); // Display any database errors
                }
                // Redirect the user back to the previous page
                header("Location: ../Projects/index.php");
                exit();
            }
    }
} else {
    // If there is no 'id' parameter in the URL, redirect to Projects/index.php
    header("Location: ../Projects/index.php");
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
        <h1>Delete Project</h1>
    </header>
    <h4>Are you sure you want to delete the project "<?php echo $project['name']; ?>"?</h4>
    <form method="POST" action="">
        <!-- Hidden Input of Selected Project To Send to Delete PHP Execution -->
        <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
        <button type="submit" class="btn btn-danger delete-buttons">Delete</button>
        <!-- If the user cancels, go back to the project list -->
        <a href="../Projects/index.php" class="btn btn-success delete-buttons">Cancel</a>
    </form>
</div>
</body>
</html>
