$(document).ready(function() {
    console.log("Document ready. Starting application script.");

    // DataTable initializations have been removed as the table is now rendered server-side.

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

    // --- Create Permission Form ---
    $('#create-permission-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $errorDiv = $('#create-error');

        $.ajax({
            url: '/permissions/create',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                permission_name: $form.find('#permission_name').val(),
                description: $form.find('#description').val()
            }),
            success: function(response) {
                if (response.status === 'success' || (response.http_code && response.http_code === 200)) {
                    // Reload the page to see the new permission
                    window.location.reload();
                } else {
                    $errorDiv.text(response.message || 'An unknown error occurred.').show();
                }
            },
            error: function(jqXHR) {
                const msg = jqXHR.responseJSON?.message || 'An error occurred.';
                $errorDiv.text(msg).show();
            }
        });
    });
    
    // --- Edit Permission ---
    // This listener is delegated from the table body, so it will work with the static table.
    $('#permissions-table-body').on('click', '.edit-btn', function() {
        const data = $(this).data('permission');
        $('#edit_permission_id').val(data.id);
        $('#edit_permission_name').val(data.permission_name);
        $('#edit_description').val(data.description);
        $('#editPermissionModal').modal('show');
    });

    $('#edit-permission-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $errorDiv = $('#edit-error');
        const permissionId = $form.find('#edit_permission_id').val();

        $.ajax({
            url: '/permissions/update',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id: permissionId,
                permission_name: $form.find('#edit_permission_name').val(),
                description: $form.find('#edit_description').val()
            }),
            success: function(response) {
                if (response.status === 'success' || (response.http_code && response.http_code === 200)) {
                     // Reload the page to see the updated permission
                    window.location.reload();
                } else {
                    $errorDiv.text(response.message || 'An unknown error occurred.').show();
                }
            },
            error: function(jqXHR) {
                const msg = jqXHR.responseJSON?.message || 'An error occurred.';
                $errorDiv.text(msg).show();
            }
        });
    });

    // --- Delete Permission ---
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
                if (response.status === 'success' || (response.http_code && response.http_code === 200)) {
                    // Reload the page to see the permission removed
                    window.location.reload();
                } else {
                    alert('Error deleting permission: ' + response.message);
                }
            },
            error: function() {
                alert('An unexpected error occurred while trying to delete the permission.');
            }
        });
    });

    // --- Modal Cleanup ---
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('.alert').hide().text('');
        $(this).find('form')[0]?.reset();
        permissionIdToDelete = null;
    });
});
