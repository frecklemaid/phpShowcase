<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $dbh;
require_once("../connection.php");
require_once ("../Auth/authenticate.php");

// Query to fetch data from the 'Projects' table
$result = $dbh->prepare("SELECT p.id, p.name, p.semester_year, p.proposal_path, CONCAT(c.fname, ' ', c.lname) AS client
                                FROM projects p
                                LEFT JOIN clients c ON p.client_id = c.id
                                GROUP BY p.id
                                ORDER BY p.id");
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
    <title>Projects</title>
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
            <li class="nav-item active">
                <a class="nav-link" href="../Projects/index.php">Projects<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../Users/index.php">Users</a>
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
        <h1>Projects</h1>
    </header>
<!--Display Projects Data in Table-->
    <div id="search-button" class="row">
        <div class="form-group col-10">
            <input type="text" class="form-control" id="searchInput" placeholder="Search">
        </div>
        <div class="col-2">
            <button type="button" name="add" id="add" class="btn btn-success btn-block" onclick="window.location.href='/Projects/add.php'">Add New Project</button>
        </div>
    </div>

<table id="projectTable" class="table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Name</th>
<!--        <th scope="col">Description</th>-->
        <th scope="col">Semester and Year</th>
        <th scope="col">Client</th>
<!--        <th scope="col">Strengths</th>-->
<!--        <th scope="col">Weaknesses</th>-->
        <th scope="col">Proposal</th>
    </tr>
    </thead>

    <tbody>
    <?php if ($result->rowCount() === 0): ?>
        <h2>Sorry, no records found.</h2>
    <?php else: ?>
    <div id="noResultsMessage" style="display: none;">
        <p>No matching projects found.</p>
    </div>

    <!--Fetch Table Data From DB and Populate-->
    <?php while ($row = $result->fetchObject()): ?>
        <tr>
            <td><?= $row->id ?></td>

            <td><?= $row->name?></td>
<!--            <td>--><?php //= $row->description?><!--</td>-->
            <td><?= $row->semester_year?></td>
            <td><?= $row->client?></td>
<!--            <td>--><?php //= $row->strengths !== null ? $row->strengths : 'N/A' ?><!--</td>-->
<!--            <td>--><?php //= $row->weaknesses !== null ? $row->weaknesses : 'N/A' ?><!--</td>-->
            <?php
            //If there is a proposal uploaded, display a link to it. Otherwise, display 'no file'.
            if ($row->proposal_path != null) {
                echo '<td><a href="' . APP_URL_PATH . '/projects_proposals/' . rawurlencode($row->proposal_path) . '" target="_blank">View File</a></td>';
            } else {
                echo '<td><p>No file</p></td>';
            }
            ?>
            <td>
                <a href="../Projects/view.php?id=<?= $row->id ?>" title="View Project"><i class="fas fa-solid fa-expand text-primary"></i></a>
            </td>
            <td>
                <a href="../Projects/update.php?id=<?= $row->id ?>" title="Edit Project"><i class="fas fa-edit text-warning"></i></a>
            </td>
            <td>
                <a href="../Projects/delete.php?id=<?= $row->id ?>" title="Delete Project"><i class="fas fa-trash-alt text-danger"></i></a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>

</table>
    <?php endif; ?>
</div>
<script>
    /**
     * Project Search Function
     * Takes searchInput from search bar as a form in put, variable to keystrokes.
     * It then filters the projects using this input.
     *
     * If there is no visible row (no result), the 'noResultsMessage' will be displayed.
     */
    $(document).ready(function () {
        $("#searchInput").on("keyup", function () {
            const value = $(this).val().toLowerCase();
            const $rows = $("#projectTable tbody tr");

            $rows.each(function () {
                const $row = $(this);
                const shouldShow = $row.text().toLowerCase().indexOf(value) > -1;
                $row.toggle(shouldShow);
            });

            // Check if there are visible rows after filtering
            const $visibleRows = $rows.filter(":visible");

            if ($visibleRows.length === 0) {
                $("#noResultsMessage").show();
            } else {
                $("#noResultsMessage").hide();
            }
        });
    });


</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
