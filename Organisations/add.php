<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve Form Data
    $name = $_POST['name'];
    $website = $_POST['website'];
    $description = $_POST['description'];
    $tech = isset($_POST['tech']) ? $_POST['tech'] : null; // Allow null
    $industry = isset($_POST['industry']) ? $_POST['industry'] : null; // Allow null
    $services = isset($_POST['services']) ? $_POST['services'] : null; // Allow null
    $field = isset($_POST['field']) ? $_POST['field'] : null; // Allow null


    try {
        // Prepare SQL statement to insert the new organization
        $orgInsertSql = "INSERT INTO organisations (name, website, description, tech, industry, services, field) 
            VALUES (:name, :website, :description, :tech, :industry, :services, :field)";
        $orgInsertStmt = $dbh->prepare($orgInsertSql);
        $orgInsertStmt->execute([
            'name' => $name,
            'website' => $website,
            'description' => $description,
            'tech' => $tech,
            'industry' => $industry,
            'services' => $services,
            'field' => $field,
        ]);

        // Get the ID of the newly inserted organization
        $this_orgID = $dbh->lastInsertId();

        // Prepare SQL statement to insert selected clients into the 'clients_organisations' table
        $clientInsertSql = "INSERT INTO clients_organisations (client_id, organisation_id) VALUES (:client_id, :organisation_id)";
        $clientInsertStmt = $dbh->prepare($clientInsertSql);

        // Insert each selected client for the organization
        foreach ($_POST['clients'] as $client) {
            $clientInsertStmt->execute([
                'client_id' => $client,
                'organisation_id' => $this_orgID,
            ]);
        }

        header('Location: index.php');
        exit(); // Add this exit to terminate the script after the redirect
    } catch (PDOException $e) {
        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';    }
} else {
    // If the form hasn't been submitted yet (i.e., when it loads), load clients from clients table as options
    $clients = [];
    $clientQuery = "SELECT id, fname, lname FROM clients";
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->execute();
    $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!-- HTML Layout For Add New Organisation Function -->
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Create Organisation</title>
</head>
<body>
<div class="form-container">
    <header>
        <h1>Create Organisation</h1>
    </header>
    <form method="POST" action="">
        <div class="form-group">
            <label for="clients">Select Clients:</label>
            <select class="form-control" id="clients" name="clients[]" multiple data-live-search="true">
                <!-- Display each client as an option for the Contact -->
                <option value="">No client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['id']; ?>">
                        <?php echo $client['fname'] . ' ' . $client['lname']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Organisation Name:</label>
            <!--Organisation Name has a maximum length of 50 characters-->
            <input type="text" name="name" id="name" class="form-control" maxlength="50" placeholder="Enter organisation name" title="Organisation name must be less than 50 characters" required>
        </div>
        <div class="form-group">
            <label for="website">Website:</label>
            <!--Website URL Input Must Include http:// or https:// and at least one character-->
            <input type="text" name="website" id="website" class="form-control" placeholder="https://companyname.com" maxlength="2083" pattern="https?://.+" title="Include http://"  required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <!--Organisation description must be a brief 1000 characters maximum as other input fields hold information-->
            <textarea type="text" name="description" id="description" class="form-control" placeholder="Enter brief description of organisation" maxlength="999" title="Description may not exceed 1000 characters" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="tech">Technology:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="tech" id="tech" maxlength="999" placeholder="Enter technology used" title="Entry may not exceed 1000 characters" class="form-control">
        </div>
        <div class="form-group">
            <label for="industry">Industry:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="industry" id="industry" maxlength="999" placeholder="Enter organisation industry" title="Entry may not exceed 1000 characters" class="form-control">
        </div>
        <div class="form-group">
            <label for="services">Services:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="services" id="services" maxlength="999" placeholder="Enter organisation services " title="Entry may not exceed 1000 characters" class="form-control">
        </div>
        <div class="form-group">
            <label for="field">Field:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="field" id="field" maxlength="999" placeholder="Enter organisation field" title="Entry may not exceed 1000 characters" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary buttons">Create Organisation</button>
        <a href="../Organisations/index.php" class="btn btn-success buttons">Cancel</a>
    </form>
</div>
</body>
</html>
