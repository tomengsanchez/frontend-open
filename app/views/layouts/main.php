<?php
// app/views/layouts/main.php
error_log("[VIEW-LAYOUT] Starting main.php layout.");
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
error_log("[VIEW-LAYOUT] User logged in status: " . ($is_logged_in ? 'Yes' : 'No'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenOffice Frontend</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php error_log("[VIEW-LAYOUT] Head section rendered."); ?>
</head>
<body>
    <?php error_log("[VIEW-LAYOUT] Body section started."); ?>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php if ($is_logged_in): ?>
        <?php error_log("[VIEW-LAYOUT] Rendering sidebar."); ?>
        <div class="bg-dark border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-white">OpenOffice</div>
            <div class="list-group list-group-flush">
                <a href="/dashboard" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="#settings-submenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-gear-fill me-2"></i>Settings</a>
                <div class="collapse" id="settings-submenu">
                    <a href="/roles" class="list-group-item list-group-item-action bg-dark text-white ps-5"><i class="bi bi-person-rolodex me-2"></i>Roles</a>
                    <a href="/permissions" class="list-group-item list-group-item-action bg-dark text-white ps-5"><i class="bi bi-shield-check me-2"></i>Permissions</a>
                    <a href="/user/list" class="list-group-item list-group-item-action bg-dark text-white ps-5"><i class="bi bi-people-fill me-2"></i>Users</a>
                </div>
            </div>
        </div>
        <?php error_log("[VIEW-LAYOUT] Sidebar rendered."); ?>
        <?php endif; ?>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <?php error_log("[VIEW-LAYOUT] Rendering page content wrapper."); ?>
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <?php if ($is_logged_in): ?>
                        <button class="btn btn-primary" id="menu-toggle"><i class="bi bi-list"></i></button>
                    <?php else: ?>
                         <a class="navbar-brand" href="#">OpenOffice Dashboard</a>
                    <?php endif; ?>

                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                             <?php if ($is_logged_in): ?>
                                <li class="nav-item">
                                    <a href="/user/logout" class="btn btn-outline-danger">Logout</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
            <?php error_log("[VIEW-LAYOUT] Navbar rendered."); ?>

            <div class="container-fluid p-4">
                 <?php
                    error_log("[VIEW-LAYOUT] Preparing to include specific view: '{$viewName}'");
                    $view_file = BASE_PATH . '/app/views/' . $viewName . '.php';
                    if (file_exists($view_file)) {
                        error_log("[VIEW-LAYOUT] View file found: {$view_file}. Including it now.");
                        include($view_file);
                        error_log("[VIEW-LAYOUT] Finished including view file: {$view_file}.");
                    } else {
                        error_log("[VIEW-LAYOUT] FATAL ERROR: View file not found at {$view_file}");
                        echo "<div class='alert alert-danger'>View '{$viewName}' not found.</div>";
                    }
                ?>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <?php error_log("[VIEW-LAYOUT] Rendering scripts."); ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Your Custom App JS -->
    <script src="/assets/js/app.js"></script>
    <script>
        // Menu Toggle Script
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
    </script>
    <?php error_log("[VIEW-LAYOUT] Finished rendering main.php layout."); ?>
</body>
</html>
