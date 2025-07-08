$(document).ready(function() {
    console.log("Document ready. Starting application script.");

    // DataTable initializations
    if ($('#users-table').length) {
        loadUsersTable();
    }
    if ($('#permissions-table').length) {
        loadPermissionsTable();
    }

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
    $('#permissions-table').on('click', '.edit-btn', function() {
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
    $('#permissions-table').on('click', '.delete-btn', function() {
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

    // --- Modal Cleanup ---
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('.alert').hide().text('');
        $(this).find('form')[0]?.reset();
        permissionIdToDelete = null;
    });

    // --- DataTable Functions ---
    function loadUsersTable() {
        // This function can be filled out later for the Users page
    }

    function loadPermissionsTable() {
        $('#permissions-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "/permissions/permissionsApi",
                "type": "GET",
                "data": function(d) {
                    return {
                        per_page: d.length,
                        page: (d.start / d.length) + 1,
                        search: d.search.value,
                        sort_by: d.columns[d.order[0].column].data,
                        sort_direction: d.order[0].dir
                    };
                },
                "error": function(xhr, error, thrown) {
                    console.error("DataTable AJAX error:", xhr.responseText);
                    // Temporarily disable the redirect to see errors in the console.
                    if (xhr.status == 401) {
                        alert("DEBUG: Received a 401 Unauthorized error. Your session might be invalid. Check the Network tab for details on the 'permissionsApi' request.");
                        // window.location.href = '/user/login';
                    } else {
                        alert("An unexpected error occurred. Please check the browser console for more details.");
                    }
                }
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
                        const permissionJson = JSON.stringify(row).replace(/'/g, "&apos;");
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
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });
    }
});
