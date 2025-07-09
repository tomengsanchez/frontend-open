<?php
// app/views/roles/index.php

function build_role_query_string($new_params) {
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
                                ID <?php if ($current_sort_by == 'id') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th>
                             <a href="?<?= build_role_query_string(['sort_by' => 'role_name', 'sort_direction' => ($current_sort_by == 'role_name' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                Role Name <?php if ($current_sort_by == 'role_name') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
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
                                    <button class="btn btn-sm btn-danger delete-role-btn" data-role-id="<?= $role['id'] ?>" data-role-name="<?= htmlspecialchars($role['role_name']) ?>">
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
            <!-- Pagination logic here -->
        <?php endif; ?>
    </div>
</div>

<!-- Delete Role Confirmation Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteRoleModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this role? This action cannot be undone.
        <p class="text-danger fw-bold mt-2" id="role-to-delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-role-delete-btn">Delete</button>
      </div>
    </div>
  </div>
</div>
