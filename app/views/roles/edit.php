<?php
// app/views/roles/edit.php

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/roles">Roles</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
<p>Update the role details and assigned permissions.</p>

<form action="/roles/update" method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($role['id']) ?>">

    <div class="card mt-4">
        <div class="card-header">
            Role Details
        </div>
        <div class="card-body">
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="role_name" class="form-label">Role Name</label>
                <input type="text" class="form-control" id="role_name" name="role_name" value="<?= htmlspecialchars($role['role_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2"><?= htmlspecialchars($role['role_description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            Assign Permissions
        </div>
        <div class="card-body">
            <div class="row">
                <?php if (!empty($groupedPermissions)): ?>
                    <?php foreach ($groupedPermissions as $group => $permissions): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <strong><?= htmlspecialchars($group) ?></strong>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($permissions as $permission): ?>
                                        <?php
                                            $isChecked = in_array($permission['id'], $assignedPermissionIds);
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>" id="perm-<?= $permission['id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="perm-<?= $permission['id'] ?>">
                                                <?= htmlspecialchars($permission['permission_name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No permissions found to assign.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="/roles" class="btn btn-secondary">Cancel</a>
    </div>
</form>
