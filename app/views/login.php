<?php // app/views/login.php ?>

<div id="login-section">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title text-center">Login</h3>
            <form id="login-form">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" value="admin" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" value="changeme" required>
                </div>
                <div id="login-error" class="alert alert-danger hidden" role="alert"></div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>
