<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once ("../Auth/authenticate.php");

// Define the number of contacts to display per page
$usersPerPage = 10;

// Get the current page number from the URL
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = intval($_GET['page']);
} else {
    $currentPage = 1; // Default to the first page
}

// Get the total number of contacts for pagination
$totalUsers = $dbh->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Calculate the total number of pages
$totalPages = ceil($totalUsers / $usersPerPage);

// Ensure that the current page does not exceed the total number of pages
if ($currentPage > $totalPages) {
    $currentPage = 1;
}

// Calculate the OFFSET for the SQL query
$offset = ($currentPage - 1) * $usersPerPage;

// Query to fetch data from the 'Clients' table along with associated organizations
$result = $dbh->prepare("SELECT id, username, password, fname, lname, email FROM users LIMIT :usersPerPage OFFSET :offset");
$result->bindParam(':usersPerPage', $usersPerPage, PDO::PARAM_INT);
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
    <title>Users</title>
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
            <li class="nav-item">
                <a class="nav-link" href="../Organisations/index.php">Organisations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Projects/index.php">Projects</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="../Users/index.php">Users<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href='../Auth/logout.php'>Logout</a>
            </li>
        </ul>
    </div>
</nav>
<body>
<div class="table-container">
<header>
<h1>Users</h1>
    <button type="button" name="add" id="add" class="btn btn-success" onclick="window.location.href='/Users/add.php'">Add New User</button>
</header>
    <!--Create Table to Display Data (Except Password)-->
<table class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Username</th>
        <th scope="col">First Name</th>
        <th scope="col">Surname</th>
        <th scope="col">Email</th>
    </tr>
    </thead>
    <!--Populate Table Body with DB Data (Except Password)-->
    <tbody>
    <?php if ($result->rowCount() === 0): ?>
        <h2>Sorry, no records found.</h2>
    <?php else: ?>
    <?php while ($row = $result->fetchObject()): ?>
        <tr>
            <td><?= $row->id ?></td>
            <td><?= $row->username?></td>
            <td><?= $row->fname?></td>
            <td><?= $row->lname?></td>
            <td><?= $row->email?></td>
            <td>
                <a href="../Users/update.php?id=<?= $row->id ?>" title="Edit User"><i class="fas fa-edit 2x text-warning"></i></a>
            </td>
            <td>
                <a href="../Users/delete.php?id=<?= $row->id ?>" title="Delete User"><i class="fas fa-trash-alt 2x text-danger"></i></a>
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
