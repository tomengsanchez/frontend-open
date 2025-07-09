<?php
// app/views/roles/index.php

// Helper function to build query strings for sorting and pagination links
function build_role_query_string($new_params) {
    $current_params = $_GET;
    $merged_params = array_merge($current_params, $new_params);
    return http_build_query($merged_params);
}

// Determine current sort parameters for active link styling
$current_sort_by = $_GET['sort_by'] ?? 'id';
$current_sort_direction = $_GET['sort_direction'] ?? 'asc';
$current_search = $_GET['search'] ?? '';
$current_per_page = $_GET['per_page'] ?? 10;

?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
        <p>From this page, you can manage user roles and their associated permissions.</p>
    </div>
    <div>
        <a href="/roles/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Role
        </a>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form action="/roles" method="GET" class="d-flex">
            <input type="text" class="form-control me-2" name="search" placeholder="Search roles..." value="<?= htmlspecialchars($current_search) ?>">
            <button type="submit" class="btn btn-info">Search</button>
             <?php if (!empty($current_search)): ?>
                <a href="/roles" class="btn btn-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>


<div class="card mt-4">
    <div class="card-header">
        <h4>Roles List</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="roles-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>
                            <a href="?<?= build_role_query_string(['sort_by' => 'id', 'sort_direction' => ($current_sort_by == 'id' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                ID
                                <?php if ($current_sort_by == 'id') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th>
                             <a href="?<?= build_role_query_string(['sort_by' => 'role_name', 'sort_direction' => ($current_sort_by == 'role_name' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                Role Name
                                <?php if ($current_sort_by == 'role_name') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="roles-table-body">
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><?= htmlspecialchars($role['id']) ?></td>
                                <td><?= htmlspecialchars($role['role_name']) ?></td>
                                <td class="text-center">
                                    <a href="/roles/edit/<?= $role['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-role-id="<?= $role['id'] ?>" data-role-name="<?= htmlspecialchars($role['role_name']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No roles found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <!-- Pagination Controls -->
        <?php if (!empty($pagination) && $pagination['total_records'] > 0): ?>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <form action="/roles" method="GET" class="d-flex align-items-center">
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
                Showing <?= count($roles) ?> of <?= $pagination['total_records'] ?> results
            </div>
            <div>
                <?php
                    $currentPage = $pagination['current_page'];
                    $totalPages = $pagination['total_pages'];
                ?>
                <nav class="d-flex align-items-center">
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($currentPage <= 1 ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_role_query_string(['page' => 1]) ?>">&laquo; First</a>
                        </li>
                        <li class="page-item <?= ($currentPage <= 1 ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_role_query_string(['page' => $currentPage - 1]) ?>">Previous</a>
                        </li>
                    </ul>
                    <form action="/roles" method="GET" class="d-flex align-items-center mx-2">
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
                            <a class="page-link" href="?<?= build_role_query_string(['page' => $currentPage + 1]) ?>">Next</a>
                        </li>
                        <li class="page-item <?= ($currentPage >= $totalPages ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_role_query_string(['page' => $totalPages]) ?>">Last &raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Note: Add Create, Edit, and Delete modals for roles here later -->
