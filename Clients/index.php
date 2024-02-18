<!--Client List Page-->
<?php
global $dbh;
require_once("../connection.php");
require_once("../Auth/authenticate.php");

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Define the number of contacts to display per page
$clientsPerPage = 5;

// Get the current page number from the URL
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = intval($_GET['page']);
} else {
    $currentPage = 1; // Default to the first page
}

// Get the total number of contacts for pagination
$totalClients = $dbh->query("SELECT COUNT(*) FROM clients")->fetchColumn();

// Calculate the total number of pages
$totalPages = ceil($totalClients / $clientsPerPage);

// Ensure that the current page does not exceed the total number of pages
if ($currentPage > $totalPages) {
    $currentPage = 1;
}

// Calculate the OFFSET for the SQL query
$offset = ($currentPage - 1) * $clientsPerPage;

// Query to fetch data from the 'Clients' table along with associated organizations
$result = $dbh->prepare("SELECT c.id, c.photo, c.fname, c.lname, c.email, c.phone, c.suburb, c.address, c.recruitment, GROUP_CONCAT(' ', o.name) AS organisations
                        FROM clients c
                        LEFT JOIN clients_organisations co ON c.id = co.client_id
                        LEFT JOIN organisations o ON co.organisation_id = o.id
                        GROUP BY c.id
                        ORDER BY c.id
                        LIMIT :clientsPerPage OFFSET :offset");
$result->bindParam(':clientsPerPage', $clientsPerPage, PDO::PARAM_INT);
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
    <title>Clients</title>
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
            <li class="nav-item active">
                <a class="nav-link" href="../Clients/index.php">Clients<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Contact/index.php">Contacts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Organisations/index.php">Organisations</a>
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
        <h1>Clients</h1>
        <button type="button" name="add" id="add" class="btn btn-success" onclick="window.location.href='../Clients/add.php'">Add New Client</button>
    </header>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Photo</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Phone</th>
            <th scope="col">Suburb</th>
            <th scope="col">Address</th>
            <th scope="col">Recruitment</th>
            <th scope="col">Organisation(s)</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->rowCount() === 0): ?>
            <h2>Sorry, no records found.</h2>
        <?php else: ?>
        <?php while ($row = $result->fetchObject()): ?>
            <tr>
                <td><?= $row->id ?></td>
                <td><img src="<?= APP_URL_PATH ?>/clients_profiles/<?= rawurlencode($row->photo) ?>" alt="Client Photo" id="profile-photo"></td> <!--Display Client Photo-->
                <td><?= $row->fname . ' ' . $row->lname ?></td>
                <td><?= $row->email?></td>
                <td><?= $row->phone?></td>
                <td><?= $row->suburb?></td>
                <td><?= $row->address?></td>
                <?php
                if ($row->recruitment != null) {
                echo "<td>$row->recruitment</td>";
                }
                else {
                    //If there is no recruitment method listed, state 'N/A'
                    echo "<td><p>N/A</p></td>";
                }
                ?>
                <td><?= $row->organisations ?></td> <!-- Display organizations -->
                <td>
                    <a href="../Contact/add.php?id=<?= $row->id ?>" title="Add New Contact"><i class="fas fa-comment-medical text-success"></i></a>
                </td>
                <td>
                    <a href="../Clients/update.php?id=<?= $row->id ?>" title="Edit Client"><i class="fas fa-edit text-warning"></i></a>
                </td>
                <td>
                    <a href="../Clients/delete.php?id=<?= $row->id ?>" title="Delete Client"><i class="fas fa-trash-alt text-danger"></i></a>
                </td>
            </tr>
        <?php endwhile; ?>
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
