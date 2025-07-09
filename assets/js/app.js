$(document).ready(function() {
    console.log("Document ready. Starting application script.");

    // --- Login Form ---
    $('#login-form').on('submit', function(e) {
        // ... login logic
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

    // --- Modal Cleanup ---
    $('.modal').on('hidden.bs.modal', function () {
        permissionIdToDelete = null;
        roleIdToDelete = null;
        $(this).find('.modal-body p.text-danger').text('');
    });
});
