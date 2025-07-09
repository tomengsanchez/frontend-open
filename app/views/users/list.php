<?php
// app/views/users/list.php

function build_user_query_string($new_params) {
    $current_params = $_GET;
    $merged_params = array_merge($current_params, $new_params);
    return http_build_query($merged_params);
}

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
        <a href="/user/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create User
        </a>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form action="/user/list" method="GET" class="d-flex">
            <input type="text" class="form-control me-2" name="search" placeholder="Search by name, username, or email..." value="<?= htmlspecialchars($current_search) ?>">
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
                             <a href="?<?= build_user_query_string(['sort_by' => 'firstname', 'sort_direction' => ($current_sort_by == 'firstname' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                Name
                                <?php if ($current_sort_by == 'firstname') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
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
                                <td><?= htmlspecialchars(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['role_name'] ?? 'N/A') ?></td>
                                <td class="text-center">
                                    <a href="/user/edit/<?= $user['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-user-btn" data-user-id="<?= $user['id'] ?>" data-user-name="<?= htmlspecialchars($user['username']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No users found.</td>
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

<!-- Note: Add Delete modal for users here later -->
