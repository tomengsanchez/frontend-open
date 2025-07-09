$(document).ready(function() {
    console.log("Document ready. Starting application script.");

    // --- Login Form ---
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $errorDiv = $('#login-error');

        $.ajax({
            url: '/user/handleLogin',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                username: $form.find('#username').val(),
                password: $form.find('#password').val()
            }),
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = '/dashboard';
                } else {
                    $errorDiv.text(response.message || 'An unknown error occurred.').removeClass('hidden');
                }
            },
            error: function(jqXHR) {
                const msg = jqXHR.responseJSON?.message || 'An error occurred during login.';
                $errorDiv.text(msg).removeClass('hidden');
            }
        });
    });

    // --- Permission Delete ---
    let permissionIdToDelete = null;
    $('#permissions-table-body').on('click', '.delete-btn', function() {
        const data = $(this).data('permission');
        permissionIdToDelete = data.id;
        $('#permission-to-delete').text(`Permission: "${data.permission_name}" (ID: ${data.id})`);
        $('#deletePermissionModal').modal('show');
    });

    $('#confirm-delete-btn').on('click', function() {
        if (!permissionIdToDelete) return;
        $.ajax({
            url: '/permissions/delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: permissionIdToDelete }),
            success: function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Error deleting permission: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('An unexpected error occurred while trying to delete the permission.');
            }
        });
    });

    // --- Role Delete ---
    let roleIdToDelete = null;
    $('#roles-table-body').on('click', '.delete-role-btn', function() {
        roleIdToDelete = $(this).data('role-id');
        const roleName = $(this).data('role-name');
        $('#role-to-delete').text(`Role: "${roleName}" (ID: ${roleIdToDelete})`);
        $('#deleteRoleModal').modal('show');
    });

    $('#confirm-role-delete-btn').on('click', function() {
        if (!roleIdToDelete) return;
        $.ajax({
            url: '/roles/delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: roleIdToDelete }),
            success: function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Error deleting role: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('An unexpected error occurred while trying to delete the role.');
            }
        });
    });

    // --- User Delete ---
    let userIdToDelete = null;
    $('#users-table-body').on('click', '.delete-user-btn', function() {
        userIdToDelete = $(this).data('user-id');
        const userName = $(this).data('user-name');
        $('#user-to-delete').text(`User: "${userName}" (ID: ${userIdToDelete})`);
        $('#deleteUserModal').modal('show');
    });

    $('#confirm-user-delete-btn').on('click', function() {
        if (!userIdToDelete) return;
        $.ajax({
            url: '/user/delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: userIdToDelete }),
            success: function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Error deleting user: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('An unexpected error occurred while trying to delete the user.');
            }
        });
    });

    // --- Modal Cleanup ---
    $('.modal').on('hidden.bs.modal', function () {
        permissionIdToDelete = null;
        roleIdToDelete = null;
        userIdToDelete = null;
        $(this).find('.modal-body p.text-danger').text('');
    });
});
