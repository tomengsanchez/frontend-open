<?php
// app/views/layouts/main.php
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

// The only helper needed here now is the AuthHelper.
require_once BASE_PATH . '/app/helpers/AuthHelper.php';

// Get the current request path to determine the active page
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Define which paths belong to the settings submenu
$settings_paths = ['/roles', '/permissions', '/user/list'];
$is_settings_page = in_array($current_path, $settings_paths);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenOffice Frontend</title>
    <!-- CSS Includes -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <?php if ($is_logged_in): ?>
        <div class="bg-dark border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-white">OpenOffice</div>
            <div class="list-group list-group-flush">
                
                <?php if (AuthHelper::can('dashboard:index')): ?>
                    <a href="/dashboard" class="list-group-item list-group-item-action bg-dark text-white <?= $current_path == '/dashboard' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <?php endif; ?>
                
                <?php // Check if user has permission to see ANY of the settings links
                $can_see_settings = AuthHelper::can('roles:index') || AuthHelper::can('permissions:index') || AuthHelper::can('users:index');
                ?>
                <?php if ($can_see_settings): ?>
                    <a href="#settings-submenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action bg-dark text-white <?= $is_settings_page ? 'active' : '' ?>"><i class="bi bi-gear-fill me-2"></i>Settings</a>
                    
                    <div class="collapse <?= $is_settings_page ? 'show' : '' ?>" id="settings-submenu">
                        <?php if (AuthHelper::can('roles:index')): ?>
                            <a href="/roles" class="list-group-item list-group-item-action bg-dark text-white ps-5 <?= $current_path == '/roles' ? 'active' : '' ?>"><i class="bi bi-person-rolodex me-2"></i>Roles</a>
                        <?php endif; ?>
                        <?php if (AuthHelper::can('permissions:index')): ?>
                            <a href="/permissions" class="list-group-item list-group-item-action bg-dark text-white ps-5 <?= $current_path == '/permissions' ? 'active' : '' ?>"><i class="bi bi-shield-check me-2"></i>Permissions</a>
                        <?php endif; ?>
                        <?php if (AuthHelper::can('users:index')): ?>
                            <a href="/user/list" class="list-group-item list-group-item-action bg-dark text-white ps-5 <?= $current_path == '/user/list' ? 'active' : '' ?>"><i class="bi bi-people-fill me-2"></i>Users</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <?php endif; ?>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
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

            <div class="container-fluid p-4">
                 <?php
                    $view_file = BASE_PATH . '/app/views/' . $viewName . '.php';
                    if (file_exists($view_file)) {
                        include($view_file);
                    } else {
                        echo "<div class='alert alert-danger'>View '{$viewName}' not found.</div>";
                    }
                ?>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- JS Includes -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
    </script>
</body>
</html>
