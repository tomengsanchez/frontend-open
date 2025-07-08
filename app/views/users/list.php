<?php
// app/views/users/list.php

// Helper function to build query strings for sorting and pagination links
function build_user_query_string($new_params) {
    $current_params = $_GET;
    // When changing page, we don't need to resubmit the per_page value unless it's different from default
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
        <p>From this page, you can manage all system users.</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-circle me-2"></i>Create User
        </button>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form action="/user/list" method="GET" class="d-flex">
            <input type="text" class="form-control me-2" name="search" placeholder="Search by username or email..." value="<?= htmlspecialchars($current_search) ?>">
            <button type="submit" class="btn btn-info">Search</button>
             <?php if (!empty($current_search)): ?>
                <a href="/user/list" class="btn btn-secondary ms-2">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>


<div class="card mt-4">
    <div class="card-header">
        <h4>Users List</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="users-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>
                            <a href="?<?= build_user_query_string(['sort_by' => 'id', 'sort_direction' => ($current_sort_by == 'id' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                ID
                                <?php if ($current_sort_by == 'id') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th>
                             <a href="?<?= build_user_query_string(['sort_by' => 'username', 'sort_direction' => ($current_sort_by == 'username' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                Username
                                <?php if ($current_sort_by == 'username') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                         <th>
                             <a href="?<?= build_user_query_string(['sort_by' => 'email', 'sort_direction' => ($current_sort_by == 'email' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                Email
                                <?php if ($current_sort_by == 'email') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th>Role</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['role_name']) ?></td>
                                <td class="text-center">
                                    <?php $userJson = htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8'); ?>
                                    <button class="btn btn-sm btn-info edit-btn" data-user='<?= $userJson ?>'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-user='<?= $userJson ?>'>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <!-- New Pagination and Rows Per Page Controls -->
        <?php if (!empty($pagination) && $pagination['total_records'] > 0): ?>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <form action="/user/list" method="GET" class="d-flex align-items-center">
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
                Showing <?= count($users) ?> of <?= $pagination['total_records'] ?> results
            </div>
            <div>
                <?php
                    $currentPage = $pagination['current_page'];
                    $totalPages = $pagination['total_pages'];
                ?>
                <nav class="d-flex align-items-center">
                    <ul class="pagination mb-0">
                        <!-- First Page Button -->
                        <li class="page-item <?= ($currentPage <= 1 ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_user_query_string(['page' => 1]) ?>">&laquo; First</a>
                        </li>
                        <!-- Previous Button -->
                        <li class="page-item <?= ($currentPage <= 1 ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_user_query_string(['page' => $currentPage - 1]) ?>">Previous</a>
                        </li>
                    </ul>
                    
                    <!-- Page Dropdown Form -->
                    <form action="/user/list" method="GET" class="d-flex align-items-center mx-2">
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
                        <!-- Next Button -->
                        <li class="page-item <?= ($currentPage >= $totalPages ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_user_query_string(['page' => $currentPage + 1]) ?>">Next</a>
                        </li>
                        <!-- Last Page Button -->
                        <li class="page-item <?= ($currentPage >= $totalPages ? 'disabled' : '') ?>">
                            <a class="page-link" href="?<?= build_user_query_string(['page' => $totalPages]) ?>">Last &raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Note: Add Create, Edit, and Delete modals for users here later -->
