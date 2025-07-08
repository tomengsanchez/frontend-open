$(document).ready(function() {

    // DataTable initializations
    if ($('#users-table').length) {
        loadUsersTable();
    }
    if ($('#permissions-table').length) {
        loadPermissionsTable();
    }

    // --- Login Form ---
    $('#login-form').on('submit', function(e) { /* ... */ });

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
                if (response.status === 'success' || response.http_code === 200) {
                    $('#createPermissionModal').modal('hide');
                    $('#permissions-table').DataTable().ajax.reload();
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
    // 1. Populate modal when an edit button is clicked
    $('#permissions-table').on('click', '.edit-btn', function() {
        const data = $(this).data('permission');
        $('#edit_permission_id').val(data.id);
        $('#edit_permission_name').val(data.permission_name);
        $('#edit_description').val(data.description);
        $('#editPermissionModal').modal('show');
    });

    // 2. Handle the form submission
    $('#edit-permission-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $errorDiv = $('#edit-error');
        const permissionId = $form.find('#edit_permission_id').val();

        $.ajax({
            url: '/permissions/update', // Using POST to send data easily
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id: permissionId,
                permission_name: $form.find('#edit_permission_name').val(),
                description: $form.find('#edit_description').val()
            }),
            success: function(response) {
                if (response.status === 'success' || response.http_code === 200) {
                    $('#editPermissionModal').modal('hide');
                    $('#permissions-table').DataTable().ajax.reload();
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
    // 1. Populate modal when a delete button is clicked
    $('#permissions-table').on('click', '.delete-btn', function() {
        const data = $(this).data('permission');
        permissionIdToDelete = data.id;
        $('#permission-to-delete').text(`Permission: "${data.permission_name}" (ID: ${data.id})`);
        $('#deletePermissionModal').modal('show');
    });

    // 2. Handle the confirmation click
    $('#confirm-delete-btn').on('click', function() {
        if (!permissionIdToDelete) return;

        $.ajax({
            url: '/permissions/delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: permissionIdToDelete }),
            success: function(response) {
                if (response.status === 'success' || response.http_code === 200) {
                    $('#deletePermissionModal').modal('hide');
                    $('#permissions-table').DataTable().ajax.reload();
                } else {
                    alert('Error deleting permission: ' + response.message);
                }
            },
            error: function() {
                alert('An unexpected error occurred while trying to delete the permission.');
            }
        });
    });


    // Clear errors and data when modals are closed
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('.alert').hide().text('');
        $(this).find('form')[0]?.reset();
        permissionIdToDelete = null;
    });


    function loadUsersTable() { /* ... */ }

    // Updated Permissions DataTable to include the Actions column
    function loadPermissionsTable() {
        $('#permissions-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "/permissions/permissionsApi",
                "type": "GET",
                // ... (data mapping remains the same)
            },
            "columns": [
                { "data": "id" },
                { "data": "permission_name" },
                { "data": "description" },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        const permissionJson = JSON.stringify(row);
                        return `
                            <button class="btn btn-sm btn-info edit-btn" data-permission='${permissionJson}'>
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-permission='${permissionJson}'>
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            // ... (other options remain the same)
        });
    }
});
