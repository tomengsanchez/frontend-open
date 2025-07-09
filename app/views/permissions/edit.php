<?php
// app/views/permissions/edit.php

// Check for session messages to display alerts
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']); // Clear message after displaying
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/permissions">Permissions</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
<p>Use the form below to update the permission details.</p>

<div class="card mt-4">
    <div class="card-header">
        Edit Permission: <?= htmlspecialchars($permission['permission_name']) ?>
    </div>
    <div class="card-body">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        
        <form action="/permissions/update" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($permission['id']) ?>">
            
            <div class="mb-3">
                <label for="permission_name" class="form-label">Permission Name</label>
                <input type="text" class="form-control" id="permission_name" name="permission_name" value="<?= htmlspecialchars($permission['permission_name']) ?>" required>
                <div class="form-text">A unique name for the permission, e.g., 'users.create', 'roles.edit'.</div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($permission['description']) ?></textarea>
                <div class="form-text">A brief explanation of what this permission allows.</div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/permissions" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
