<!--Projects Modification Function-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if (isset($_GET['id'])) {
    // Fetch project details for editing
    $project_id = $_GET['id'];
    $projectQuery = "SELECT * FROM projects WHERE id=?";
    $projectStmt = $dbh->prepare($projectQuery);
    $projectStmt->execute([$project_id]);
    $project = $projectStmt->fetch(PDO::FETCH_ASSOC);
    if (!$project) {
        header("Location: ../Projects/index.php");
    }

    //Fetch all clients
    $clients = [];
    $clientQuery = "SELECT id, fname, lname FROM clients";
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->execute();
    $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);

    //Retrieve this projects client
    $current_client = $project['client_id'];


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Retrieve file changes (if there)
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../projects_proposals/'; // Save files to clients_profiles directory

            //Check if there was already a file
            $existingFile = '../projects_proposals/' . $project["proposal_path"]; // Set the path to the existing file
            if (file_exists($existingFile)) {
                unlink($existingFile);
            }

            // Generate a unique filename to avoid overwriting existing files
            $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                // File uploaded successfully, you can proceed with further processing
            } else {
                throw new Exception("Cannot store file. See warning for more information.");
            }
        }else {
            throw new Exception("Uploaded file cannot be processed. Error code: " . $_FILES['file']['error']);
        }

        // Retrieve any changes here

        try {
            // Handle the form submission to update a project
            $new_file = $uploadFile !== null ? $uploadFile : null; //set new file to uploaded new file if it exists
            $new_client_id = $_POST['client_id'];
            $new_name = $_POST['name'];
            $new_description = $_POST['description'];
            $new_semester_year = $_POST['semester_year'];
            $new_strengths = isset($_POST['strengths']) ? $_POST['strengths'] : null;
            $new_weaknesses = isset($_POST['weaknesses']) ? $_POST['weaknesses'] : null;

            // Perform the database update
            $updateQuery = "UPDATE projects
            SET client_id=?, name=?, description=?, semester_year=?, strengths=?, weaknesses=?, proposal_path=?
            WHERE id=?";
            $updateStmt = $dbh->prepare($updateQuery);
            $updateStmt->execute([$new_client_id, $new_name, $new_description, $new_semester_year, $new_strengths, $new_weaknesses, $new_file, $project_id]);

            // Redirect to the project list page after updating the project
            header("Location: ../Projects/index.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Display any database errors
        }
    }
} else {
    // If there is no 'id' parameter in the URL, redirect to Projects/index.php
    header("Location: ../Projects/index.php");
    exit();
}
?>


<!-- Update Project Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Update Project</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="form-container">
    <h1>Update Project</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
        </div>
        <div class="form-group">
            <label for="client_id">Client ID:</label>
            <select name="client_id" id="client_id" class="form-control" required>
                <!-- Display each client as an option for the project -->
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['id']; ?>" <?php echo (isset($current_client) && $client['id'] == $current_client) ? 'selected' : ''; ?>>
                        <?php echo $client['fname'] . ' ' . $client['lname']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Project Name:</label>
            <!--Project Name has maxlength of 254 characters-->
            <input type="text" name="name" class="form-control" maxlength="254" value="<?php echo $project['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="semester_year">Semester and Year:</label>
            <!--Semester and Year Must Meet the Pattern of S(1 or 2) + Year in 4 digits-->
            <input type="text" name="semester_year" id="semester_year" class="form-control" maxlength="8" pattern="^S[1-2]\s\d{4}$" value="<?php echo $project['semester_year']; ?>" required>
        </div>
        <div class="form-group">
            <label for="strengths">Strengths:</label>
            <!--Max length of 1000 characters-->
            <input type="text" name="strengths" id="strengths" class="form-control" maxlength="1000" value="<?php echo $project['strengths']; ?>">
        </div>
        <div class="form-group">
            <label for="weaknesses">Weaknesses:</label>
            <!--Max length of 1000 characters-->
            <input type="text" name="weaknesses" id="weaknesses" class="form-control" maxlength="1000" value="<?php echo $project['weaknesses']; ?>">
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <!--Max length of 8000 characters-->
            <textarea name="description" id="description" class="form-control" rows="5" maxlength="8000" required><?php echo $project['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="file">Project Proposal:</label>
            <!--Display Current Proposal File-->
            <?php
            // If there is a proposal uploaded, display a link to it.
            if (isset($project['proposal_path']) && $project['proposal_path'] !== null) {
                echo '<a href="' . APP_URL_PATH . '/Projects/projects_proposals/' . rawurlencode($project['proposal_path']) . '" target="_blank">View Attached Proposal</a>';
            }
            ?> <br>
            <!--Allow Only Text and Powerpoint File Types-->
            <input type="file" id="file" name="file" accept=".pdf, .docx, .odt, .odp, .html, .txt, .doc, .ppt" value="">
        </div>
        <button type="submit" class="btn btn-danger buttons" value="Update">Update Project</button>
        <a href="../Projects/index.php" class="btn btn-success buttons">Cancel</a>
    </form>
</div>
</body>
</html>