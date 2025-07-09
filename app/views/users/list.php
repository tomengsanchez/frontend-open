<?php
// app/views/users/list.php

function build_user_query_string($new_params) {
    $current_params = $_GET;
    $merged_params = array_merge($current_params, $new_params);
    return http_build_query($merged_params);
}

// ... (other variables)

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

<!-- ... (Search Form) ... -->

<div class="card mt-4">
    <div class="card-header">
        <h4>Users List</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="users-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <!-- ... (table headers) ... -->
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
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <!-- ... (pagination controls) ... -->
    </div>
</div>

<!-- Note: Add Delete modal for users here later -->
