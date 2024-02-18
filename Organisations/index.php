<?php
global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Define the number of contacts to display per page
$orgsPerPage = 10;

// Get the current page number from the URL
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = intval($_GET['page']);
} else {
    $currentPage = 1; // Default to the first page
}

// Get the total number of contacts for pagination
$totalOrgs = $dbh->query("SELECT COUNT(*) FROM organisations")->fetchColumn();

// Calculate the total number of pages
$totalPages = ceil($totalOrgs / $orgsPerPage);

// Ensure that the current page does not exceed the total number of pages
if ($currentPage > $totalPages) {
    //If user inputs a page that doesn't exist, send them back to first page
    $currentPage = 1;
}

// Calculate the OFFSET for the SQL query
$offset = ($currentPage - 1) * $orgsPerPage;

$result = $dbh->prepare("SELECT o.id, o.name, o.website, o.description, o.tech, o.industry, o.services, o.field, GROUP_CONCAT(' ', c.fname, ' ', c.lname) AS clients
                        FROM organisations o
                        LEFT JOIN clients_organisations co ON o.id = co.organisation_id
                        LEFT JOIN clients c ON co.client_id = c.id
                        GROUP BY o.id
                        ORDER BY o.id
                        LIMIT :orgsPerPage OFFSET :offset");
$result->bindParam(':orgsPerPage', $orgsPerPage, PDO::PARAM_INT);
$result->bindParam(':offset', $offset, PDO::PARAM_INT);
$result->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
    <title>Organisations</title>
</head>
<nav class="navbar navbar-expand-lg navbar navbar-dark bg-primary">
    <a class="navbar-brand" href="../dash.php">N. Altman Recruiting</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Home
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../dash.php">Dash</a>
                    <a class="dropdown-item" href="../index.php">Customer Home</a>
                    <a class="dropdown-item" href="../Contact/contactForm.php">Contact Form</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Clients/index.php">Clients</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Contact/index.php">Contacts</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="../Organisations/index.php">Organisations <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Projects/index.php">Projects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Users/index.php">Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Auth/logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<body>
<div class="table-container">
<header>
<h1>Organisations</h1>
    <button type="button" name="add" id="add" class="btn btn-success" onclick="window.location.href='/Organisations/add.php'">Add New Organisation</button>
</header>
    <table class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Name</th>
        <th scope="col">Website</th>
        <th scope="col">Client(s)</th>
<!--        <th scope="col">Description</th>-->
<!--        <th scope="col">Technology</th>-->
<!--        <th scope="col">Industry</th>-->
<!--        <th scope="col">Services</th>-->
<!--        <th scope="col">Field</th>-->

    </tr>
    </thead>
    <tbody>
    <?php if ($result->rowCount() === 0): ?>
        <h2>Sorry, no records found.</h2>
    <?php else: ?>
    <?php
    $currentOrgId = null; // To keep track of the current organization ID
    while ($row = $result->fetchObject()):
        if ($row->id !== $currentOrgId):
            // Start a new row for each organization
            ?>
            <tr>
                <td><?= $row->id ?></td>
                <td><?= $row->name ?></td>
                 <td><a href="<?= $row->website ?>" target="_blank" title="Opens Organisation Site">Open Site</a></td>
                <!--                <td>--><?php //= $row->description ?><!--</td>-->
<!--                <td>--><?php //= !empty($row->tech) ? $row->tech : "N/A" ?><!--</td>-->
<!--                <td>--><?php //= !empty($row->industry) ? $row->industry : "N/A" ?><!--</td>-->
<!--                <td>--><?php //= !empty($row->services) ? $row->services : "N/A" ?><!--</td>-->
<!--                <td>--><?php //= !empty($row->field) ? $row->field : "N/A" ?><!--</td>-->
                <td>
                    <?= $row->clients ?>
                </td>
                <td>
                    <a href="../Organisations/view.php?id=<?= $row->id ?>" title="View Organisation"><i class="fas fa-solid fa-expand text-primary"></i></a>
                </td>
                <td>
                    <a href="../Organisations/update.php?id=<?= $row->id ?>" title="Edit Organisation"><i class="fas fa-edit text-warning"></i></a>
                </td>
                <td>
                    <a href="../Organisations/delete.php?id=<?= $row->id ?>" title="Delete Organisation"><i class="fas fa-trash-alt text-danger"></i></a>
                </td>
            </tr>
        <?php
        else:
            // Append client information to the current organization's cell
            ?>
            <td><?= $row->client_fname ?> <?= $row->client_lname ?><br></td>
        <?php
        endif;
        $currentOrgId = $row->id; // Update the current organization ID
    endwhile;
    ?>
    <?php endif; ?>
    </tbody>
</table>
    <!-- Pagination Links -->
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php if ($i === $currentPage) echo 'active'; ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
