$(document).ready(function() {

    // If the users-table exists, initialize its DataTable
    if ($('#users-table').length) {
        loadUsersTable();
    }

    // If the permissions-table exists, initialize its DataTable
    if ($('#permissions-table').length) {
        loadPermissionsTable();
    }

    // --- Login Form ---
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        // ... (login logic remains the same)
    });

    // --- Create Permission Form ---
    $('#create-permission-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $errorDiv = $('#create-error');

        const permissionName = $form.find('#permission_name').val();
        const description = $form.find('#description').val();

        $.ajax({
            url: '/permissions/create',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                permission_name: permissionName,
                description: description
            }),
            success: function(response) {
                if (response.status === 'success' || response.http_code === 200) {
                    // Hide the modal
                    $('#createPermissionModal').modal('hide');
                    // Reset the form
                    $form[0].reset();
                    // Reload the DataTable
                    $('#permissions-table').DataTable().ajax.reload();
                } else {
                    const message = response.message || 'An unknown error occurred.';
                    $errorDiv.text(message).show();
                }
            },
            error: function(jqXHR) {
                let errorMessage = 'An error occurred.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                }
                $errorDiv.text(errorMessage).show();
            }
        });
    });
    
    // Clear error message when modal is closed
    $('#createPermissionModal').on('hidden.bs.modal', function () {
        $('#create-error').hide().text('');
        $('#create-permission-form')[0].reset();
    });


    // Function to initialize and load the Users DataTable
    function loadUsersTable() {
        // ... (users table logic remains the same)
    }

    // Function to initialize and load the Permissions DataTable
    function loadPermissionsTable() {
        // ... (permissions table logic remains the same)
    }
});
