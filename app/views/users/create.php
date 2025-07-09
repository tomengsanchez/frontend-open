<?php
// app/views/users/create.php

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/user/list">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
</nav>

<h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
<p>Fill out the form below to add a new user to the system.</p>

<div class="card mt-4">
    <div class="card-header">
        New User Details
    </div>
    <div class="card-body">
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        
        <form action="/user/store" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
             <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label">Assign Role</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="" selected disabled>Select a role...</option>
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role['id']) ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save User</button>
                <a href="/user/list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
