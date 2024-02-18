<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Prepare the database insert
        $sql = "INSERT INTO projects (client_id, name, description, semester_year, strengths, weaknesses, proposal_path)
            VALUES (:client_id, :name, :description, :semester_year, :strengths, :weaknesses, :proposal_path)";
        $stmt = $dbh->prepare($sql);

        try {
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../projects_proposals/'; // Specify the directory where you want to save the uploaded files

                // Generate a unique filename to avoid overwriting existing files
                $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['file']['name']);

                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                    // File uploaded successfully, you can proceed with further processing
                } else {
                    throw new Exception("Cannot store file. See warning for more information.");
                }
            } else {
                throw new Exception("Uploaded file cannot be processed. Error code: " . $_FILES['file']['error']);
            }

            // Execute the query
            $stmt->execute([
                'client_id' => !empty($_POST['client_id']) ? $_POST['client_id'] : null,
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'semester_year' => $_POST['semester_year'],
                'strengths' => !empty($_POST['strengths']) ? $_POST['strengths'] : null,
                'weaknesses' => !empty($_POST['weaknesses']) ? $_POST['weaknesses'] : null,
                'proposal_path' => $uploadFile !== null ? $uploadFile : null,
            ]);
            header('Location: index.php');
            exit(); // Add this exit to terminate the script after the redirect
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Display any database errors
        }

    } else {
        //If the form hasn't been submitted yet (ie, when it loads in) load clients from clients table as options
        $clients = [];
        $clientQuery = "SELECT id, fname, lname FROM clients";
        $clientStmt = $dbh->prepare($clientQuery);
        $clientStmt->execute();
        $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Create Project</title>
</head>
<body>
<div class="form-container">
<header>
<h1>Create Project</h1>
</header>
<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="client_id">Client ID:</label>
        <select name="client_id" id="client_id" class="form-control" required>
            <!-- Display each client as an option for the project -->
            <?php foreach ($clients as $client): ?>
                <option value="<?php echo $client['id']; ?>">
                    <?php echo $client['fname'] . ' ' . $client['lname']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="name">Project Name:</label>
        <input type="text" name="name" id="name" class="form-control" maxlength="254" required>
    </div>
    <div class="form-group">
        <label for="semester_year">Semester and Year (Sx YYYY):</label>
        <!--Semester and Year Must Meet the Pattern of S(1 or 2) + Year in 4 digits-->
        <input type="text" name="semester_year" id="semester_year" class="form-control" placeholder="Example: S1 2023" maxlength="8" pattern="^S[1-2]\s\d{4}$" required>
    </div>
    <div class="form-group">
        <label for="strengths">Strengths:</label>
        <input type="text" name="strengths" id="strengths" class="form-control" maxlength="1000">
    </div>
    <div class="form-group">
        <label for="weaknesses">Weaknesses:</label>
        <input type="text" name="weaknesses" id="weaknesses" class="form-control" maxlength="1000">
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description" class="form-control" rows="5" required maxlength="8000"></textarea>
    </div>
    <div class="form-group">
        <label for="exhibit-file">Proposal File:</label><br>
        <!--Allow Only Text and Powerpoint File Types-->
        <input type="file" id="file" name="file" accept=".pdf, .docx, .odt, .odp, .html, .txt, .doc, .ppt" value="">
    </div>
    <button type="submit" class="btn btn-primary buttons">Create Project</button>
    <a href="../Projects/index.php" class="btn btn-success buttons">Cancel</a>

</form>
</div>
</body>
</html>
