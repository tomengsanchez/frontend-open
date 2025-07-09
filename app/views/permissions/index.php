<?php
// app/views/permissions/index.php

// Helper function to build query strings for sorting and pagination links
function build_permission_query_string($new_params) {
    $current_params = $_GET;
    $merged_params = array_merge($current_params, $new_params);
    return http_build_query($merged_params);
}

// Determine current sort parameters for active link styling
$current_sort_by = $_GET['sort_by'] ?? 'id';
$current_sort_direction = $_GET['sort_direction'] ?? 'asc';
$current_search = $_GET['search'] ?? '';
$current_per_page = $_GET['per_page'] ?? 10;

// Check for session messages to display alerts
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']); // Clear message after displaying

?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
        <p>From this page, you can manage all system permissions.</p>
    </div>
    <div>
        <a href="/permissions/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Permission
        </a>
    </div>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form action="/permissions" method="GET" class="d-flex">
            <input type="text" class="form-control me-2" name="search" placeholder="Search permissions..." value="<?= htmlspecialchars($current_search) ?>">
            <button type="submit" class="btn btn-info">Search</button>
             <?php if (!empty($current_search)): ?>
                <a href="/permissions" class="btn btn-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>


<div class="card mt-4">
    <div class="card-header">
        <h4>Permissions List</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="permissions-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>
                            <a href="?<?= build_permission_query_string(['sort_by' => 'id', 'sort_direction' => ($current_sort_by == 'id' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                ID
                                <?php if ($current_sort_by == 'id') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th>
                             <a href="?<?= build_permission_query_string(['sort_by' => 'permission_name', 'sort_direction' => ($current_sort_by == 'permission_name' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                Permission Name
                                <?php if ($current_sort_by == 'permission_name') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th>Description</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="permissions-table-body">
                    <?php if (!empty($permissions)): ?>
                        <?php foreach ($permissions as $permission): ?>
                            <tr>
                                <td><?= htmlspecialchars($permission['id']) ?></td>
                                <td><?= htmlspecialchars($permission['permission_name']) ?></td>
                                <td><?= htmlspecialchars($permission['description']) ?></td>
                                <td class="text-center">
                                    <!-- Edit button is now a link -->
                                    <a href="/permissions/edit/<?= $permission['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <?php $permissionJson = htmlspecialchars(json_encode($permission), ENT_QUOTES, 'UTF-8'); ?>
                                    <button class="btn btn-sm btn-danger delete-btn" data-permission='<?= $permissionJson ?>'>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No permissions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <!-- Pagination Controls -->
        <?php if (!empty($pagination) && $pagination['total_records'] > 0): ?>
            <!-- Pagination logic remains the same -->
        <?php endif; ?>
    </div>
</div>

<!-- Delete Modal is still needed -->
<div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
  <!-- ... modal content ... -->
</div>
