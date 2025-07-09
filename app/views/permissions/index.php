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
        <!-- Standard Pagination Controls -->
        <?php if (!empty($pagination) && $pagination['total_records'] > 0): ?>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <form action="/permissions" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($current_search) ?>">
                    <input type="hidden" name="sort_by" value="<?= htmlspecialchars($current_sort_by) ?>">
                    <input type="hidden" name="sort_direction" value="<?= htmlspecialchars($current_sort_direction) ?>">
                    <label for="per_page" class="form-label me-2 mb-0">Rows:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <?php $per_page_options = [10, 25, 50, 100]; ?>
                        <?php foreach ($per_page_options as $option): ?>
                            <option value="<?= $option ?>" <?= ($current_per_page == $option) ? 'selected' : '' ?>><?= $option ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <div class="text-muted">
                Showing <?= count($permissions) ?> of <?= $pagination['total_records'] ?> results
            </div>
            <div>
                <?php
                    $currentPage = $pagination['current_page'];
                    $totalPages = $pagination['total_pages'];
                ?>
                <nav class="d-flex align-items-center">
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($currentPage <= 1 ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_permission_query_string(['page' => 1]) ?>">&laquo; First</a>
                        </li>
                        <li class="page-item <?= ($currentPage <= 1 ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_permission_query_string(['page' => $currentPage - 1]) ?>">Previous</a>
                        </li>
                    </ul>
                    <form action="/permissions" method="GET" class="d-flex align-items-center mx-2">
                         <input type="hidden" name="search" value="<?= htmlspecialchars($current_search) ?>">
                         <input type="hidden" name="sort_by" value="<?= htmlspecialchars($current_sort_by) ?>">
                         <input type="hidden" name="sort_direction" value="<?= htmlspecialchars($current_sort_direction) ?>">
                         <input type="hidden" name="per_page" value="<?= htmlspecialchars($current_per_page) ?>">
                         <label for="page" class="form-label me-2 mb-0">Page:</label>
                         <select name="page" id="page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <option value="<?= $i ?>" <?= ($currentPage == $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                         </select>
                    </form>
                     <ul class="pagination mb-0">
                        <li class="page-item <?= ($currentPage >= $totalPages ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_permission_query_string(['page' => $currentPage + 1]) ?>">Next</a>
                        </li>
                        <li class="page-item <?= ($currentPage >= $totalPages ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_permission_query_string(['page' => $totalPages]) ?>">Last &raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deletePermissionModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this permission? This action cannot be undone.
        <p class="text-danger fw-bold mt-2" id="permission-to-delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
      </div>
    </div>
  </div>
</div>
