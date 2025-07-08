<?php
// app/views/permissions/create.php

// Check for session messages to display alerts
$error_message = $_SESSION['error_message'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['error_message'], $_SESSION['success_message']); // Clear messages after displaying
?>

<h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
<p>Fill out the form below to add a new permission to the system.</p>

<div class="card mt-4">
    <div class="card-header">
        New Permission Details
    </div>
    <div class="card-body">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        
        <form action="/permissions/store" method="POST">
            <div class="mb-3">
                <label for="permission_name" class="form-label">Permission Name</label>
                <input type="text" class="form-control" id="permission_name" name="permission_name" required>
                <div class="form-text">A unique name for the permission, e.g., 'users.create', 'roles.edit'.</div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                <div class="form-text">A brief explanation of what this permission allows.</div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save Permission</button>
                <a href="/permissions" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
