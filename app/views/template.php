<?php
// app/views/template.php
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenOffice Frontend</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .hidden { display: none; }
        #login-section .card { max-width: 400px; margin: 50px auto; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">OpenOffice Dashboard</a>
            <?php if ($is_logged_in): ?>
                <a href="/user/logout" class="btn btn-outline-light">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container mt-4">
        <?php
        // This is where the specific view content will be loaded
        $view_file = BASE_PATH . '/app/views/' . $viewName . '.php';
        if (file_exists($view_file)) {
            include($view_file);
        } else {
            echo "<p>View '{$viewName}' not found.</p>";
        }
        ?>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Your Custom App JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
