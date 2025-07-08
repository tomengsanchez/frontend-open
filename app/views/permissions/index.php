<?php
// app/views/permissions/index.php

// Helper function to build query strings for sorting and pagination links
function build_query_string($new_params) {
    // Start with the existing GET parameters
    $current_params = $_GET;
    // Merge new parameters, overwriting existing ones if necessary
    $merged_params = array_merge($current_params, $new_params);
    // Build and return the query string
    return http_build_query($merged_params);
}

// Determine current sort parameters for active link styling
$current_sort_by = $_GET['sort_by'] ?? 'id';
$current_sort_direction = $_GET['sort_direction'] ?? 'asc';
$current_search = $_GET['search'] ?? '';

?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
        <p>From this page, you can manage all system permissions.</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
            <i class="bi bi-plus-circle me-2"></i>Create Permission
        </button>
    </div>
</div>

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
                            <a href="?<?= build_query_string(['sort_by' => 'id', 'sort_direction' => ($current_sort_by == 'id' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
                                ID
                                <?php if ($current_sort_by == 'id') echo $current_sort_direction == 'asc' ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>'; ?>
                            </a>
                        </th>
                        <th>
                             <a href="?<?= build_query_string(['sort_by' => 'permission_name', 'sort_direction' => ($current_sort_by == 'permission_name' && $current_sort_direction == 'asc') ? 'desc' : 'asc']) ?>">
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
                                    <?php $permissionJson = htmlspecialchars(json_encode($permission), ENT_QUOTES, 'UTF-8'); ?>
                                    <button class="btn btn-sm btn-info edit-btn" data-permission='<?= $permissionJson ?>'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
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
        <!-- Pagination -->
        <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item"><a class="page-link" href="?<?= build_query_string(['page' => $pagination['current_page'] - 1]) ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?= ($i == $pagination['current_page']) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= build_query_string(['page' => $i]) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <li class="page-item"><a class="page-link" href="?<?= build_query_string(['page' => $pagination['current_page'] + 1]) ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Modals (Create, Edit, Delete) -->
<!-- Create Permission Modal -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createPermissionModalLabel">Create New Permission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="create-permission-form">
          <div class="mb-3">
            <label for="permission_name" class="form-label">Permission Name</label>
            <input type="text" class="form-control" id="permission_name" name="permission_name" required>
            <div class="form-text">e.g., 'users.create', 'roles.edit'</div>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
           <div id="create-error" class="alert alert-danger" style="display: none;"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="create-permission-form">Save Permission</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="edit-permission-form">
          <input type="hidden" id="edit_permission_id" name="id">
          <div class="mb-3">
            <label for="edit_permission_name" class="form-label">Permission Name</label>
            <input type="text" class="form-control" id="edit_permission_name" name="permission_name" required>
          </div>
          <div class="mb-3">
            <label for="edit_description" class="form-label">Description</label>
            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
          </div>
          <div id="edit-error" class="alert alert-danger" style="display: none;"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="edit-permission-form">Save Changes</button>
      </div>
    </div>
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
