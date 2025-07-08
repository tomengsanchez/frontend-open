<?php // app/views/users/list.php ?>

<h1 class="mt-4"><?= htmlspecialchars($title) ?></h1>
<p>From this page, you can manage all the users in the system.</p>

<div class="card mt-4">
    <div class="card-header">
        <h4>Users List</h4>
    </div>
    <div class="card-body">
        <table id="users-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded by DataTables -->
            </tbody>
        </table>
    </div>
</div>
