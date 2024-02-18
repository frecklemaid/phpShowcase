<!--Organisation Modification Function-->
<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

//Check if there is a valid client passed through
if (isset($_GET['id'])) {
    // Fetch the organisation record based on the 'id' in URL
    $organisation_id = $_GET['id'];
    $orgQuery = "SELECT * FROM organisations WHERE id = :organisation_id";
    $orgStmt = $dbh->prepare($orgQuery);
    $orgStmt->bindParam(':organisation_id', $organisation_id, PDO::PARAM_INT); // Corrected binding
    $orgStmt->execute();
    $organisation = $orgStmt->fetch(PDO::FETCH_ASSOC);
    if (!$organisation) {
        header("Location: ../Organisations/index.php");
        exit(); // Add exit() to terminate the script
    }
    //Fetch the clients
    $clients = [];
    $clientQuery = "SELECT id, fname, lname FROM clients";
    $clientStmt = $dbh->prepare($clientQuery);
    $clientStmt->execute();
    $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);

    //Fetch the clients that are associated with this organisationn for preselect
    $clientsOrganisations = [];
    $coQuery = "SELECT client_id FROM clients_organisations WHERE organisation_id = :organisation_id";
    $coStmt = $dbh->prepare($coQuery);
    $coStmt->bindParam(':organisation_id', $organisation_id, PDO::PARAM_INT); // Use 'PDO::PARAM_INT' instead of 'type:'
    $coStmt->execute();
    // Fetch and store the results in the $clientsOrganisations array
    while ($row = $coStmt->fetch(PDO::FETCH_ASSOC)) {
        $clientsOrganisations[] = $row['client_id'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve Form Data
    $new_orgId = $_POST['org_id'];
    $new_clients = isset($_POST['clients']) ? $_POST['clients'] : null;
    $new_name = $_POST['name'];
    $new_website = $_POST['website'];
    $new_description = $_POST['description'];
    $new_tech = isset($_POST['tech']) ? $_POST['tech'] : null;
    $new_industry = isset($_POST['industry']) ? $_POST['industry'] : null;
    $new_services = isset($_POST['services']) ? $_POST['services'] : null;
    $new_field = isset($_POST['field']) ? $_POST['field'] : null;

    try {
        $updateQuery = "UPDATE organisations SET name = :name, website = :website, description = :description,
                         tech = :tech, industry = :industry, services = :services, field = :field
                        WHERE id = :organisation_id";

        $updateStmt = $dbh->prepare($updateQuery);

        // Define an associative array with placeholders as keys and values
        $params = [
            ':name' => $new_name,
            ':website' => $new_website,
            ':description' => $new_description,
            ':tech' => $new_tech,
            ':industry' => $new_industry,
            ':services' => $new_services,
            ':field' => $new_field,
            ':organisation_id' => $new_orgId,
        ];

     // Execute the statement with the associative array
        if (!$updateStmt->execute($params))  {
            throw new Exception("The update statement could not be completed.");
        }

        // Handle updating clients here
        $deleteClientsSql = "DELETE FROM clients_organisations WHERE organisation_id = :organisation_id";
        $deleteClientsStmt = $dbh->prepare($deleteClientsSql);
        $deleteClientsStmt->bindParam(':organisation_id', $organisation_id, PDO::PARAM_INT);
        $deleteClientsStmt->execute();

        $clientInsertSql = "INSERT INTO clients_organisations (client_id, organisation_id) VALUES (:client_id, :organisation_id)";
        $clientStmt = $dbh->prepare($clientInsertSql);

        // Insert each selected organization for the client
        foreach ($new_clients as $client_id) {
            $clientStmt->execute([
                'client_id' => $client_id,
                'organisation_id' => $organisation_id,
            ]);
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Display any database errors
    }

        header("Location: ../Organisations/index.php");
exit();

    }

}

else {
    //If there is no client ID in URL, redirect to organisations index for organisation selection.
    header("Location: ../Organisations/index.php");
    exit();
}
?>
<!-- HTML Layout For Update Organisation Function -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Update Organisation</title>
</head>
<body>
<div class="form-container">
    <header>
        <h1>Update Organisation</h1>
    </header>
    <form method="POST" action="">
        <input type="hidden" name="org_id" value="<?php echo $organisation['id']; ?>">
        <div class="form-group">
            <label for="clients">Select Clients:</label>
            <select class="form-control" id="clients" name="clients[]" multiple data-live-search="true">
                <!-- Display each client as an option for the Contact -->
                <option value="">No client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['id']; ?>" <?php echo in_array($client['id'], $clientsOrganisations) ? 'selected' : ''; ?>>
                        <?php echo $client['fname'] . ' ' . $client['lname']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="name">Organisation Name:</label>
            <!--Organisation Name has a maximum length of 50 characters-->
            <input type="text" name="name" id="name" class="form-control" maxlength="50" placeholder="Enter organisation name" title="Organisation name must be less than 50 characters" value="<?php echo $organisation['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="website">Website:</label>
            <!--Website URL Input Must Include http:// or https:// and at least one character-->
            <input type="text" name="website" id="website" class="form-control" placeholder="https://companyname.com" maxlength="2083" pattern="https?://.+" title="Include http://" value="<?php echo $organisation['website']; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <!--Organisation description must be a brief 1000 characters maximum as other input fields hold information-->
            <textarea type="text" name="description" id="description" placeholder="Enter brief description of organisation" maxlength="1000" title="Description may not exceed 1000 characters" class="form-control" rows="5" required><?php echo $organisation['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="tech">Technology:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="tech" id="tech" class="form-control" maxlength="1000" placeholder="Enter technology used" title="Entry may not exceed 1000 characters" value="<?php echo $organisation['tech']; ?>">
        </div>
        <div class="form-group">
            <label for="industry">Industry:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="industry" id="industry" class="form-control" maxlength="1000" placeholder="Enter organisation industry" title="Entry may not exceed 1000 characters" value="<?php echo $organisation['industry']; ?>">
        </div>
        <div class="form-group">
            <label for="services">Services:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="services" id="services" class="form-control" maxlength="1000" placeholder="Enter organisation services " title="Entry may not exceed 1000 characters" value="<?php echo $organisation['services']; ?>">
        </div>
        <div class="form-group">
            <label for="field">Field:</label>
            <!--Maximum 1000 Character Input-->
            <input type="text" name="field" id="field" class="form-control" maxlength="1000" placeholder="Enter organisation field " title="Entry may not exceed 1000 characters" value="<?php echo $organisation['field']; ?>">
        </div>
        <button type="submit" class="btn btn-primary buttons">Update Organisation</button>
        <a href="../Organisations/index.php" class="btn btn-success buttons">Cancel</a>
    </form>

</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

