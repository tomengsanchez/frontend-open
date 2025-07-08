$(document).ready(function() {

    // Check if the main content is visible on page load, which means the user is logged in.
    if ($('#main-content').is(':visible')) {
        loadUsersTable();
    }

    // Handle Login Form Submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission

        const username = $('#username').val();
        const password = $('#password').val();
        const $loginError = $('#login-error');

        $.ajax({
            url: 'proxy_api.php?endpoint=/login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                password: password
            }),
            success: function(response) {
                if (response.status === 'success') {
                    // Hide login form and show main content
                    $('#login-section').addClass('hidden');
                    $('#main-content').removeClass('hidden');
                    $('#logout-button').removeClass('hidden');
                    $loginError.addClass('hidden');

                    // Load the data into the table
                    loadUsersTable();
                } else {
                    // Show error message from the API
                    $loginError.text(response.message || 'An unknown error occurred.').removeClass('hidden');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle network errors or other issues
                let errorMessage = 'An error occurred during login.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                }
                $loginError.text(errorMessage).removeClass('hidden');
            }
        });
    });

    // Handle Logout Button Click
    $('#logout-button').on('click', function() {
        $.ajax({
            url: 'proxy_api.php?endpoint=/logout',
            type: 'POST',
            success: function() {
                // Easiest way to reset the state is to reload the page.
                location.reload();
            },
            error: function() {
                // Still reload even if there's an error, to log the user out on the frontend.
                location.reload();
            }
        });
    });


    // Function to initialize and load the DataTable
    function loadUsersTable() {
        // If the table instance already exists, destroy it first
        if ($.fn.DataTable.isDataTable('#users-table')) {
            $('#users-table').DataTable().destroy();
        }

        const usersTable = $('#users-table').DataTable({
            "processing": true, // Show processing indicator
            "serverSide": true, // Enable server-side processing
            "ajax": {
                "url": "proxy_api.php?endpoint=/settings/users",
                "type": "GET",
                "data": function(d) {
                    // Map DataTables parameters to what our API expects
                    const params = {
                        per_page: d.length,
                        page: (d.start / d.length) + 1,
                        search: d.search.value
                    };

                    // Handle sorting
                    if (d.order && d.order.length > 0) {
                        const order = d.order[0];
                        const columnIndex = order.column;
                        const columnName = d.columns[columnIndex].data;
                        params.sort_by = mapColumnName(columnName);
                        params.sort_direction = order.dir;
                    }

                    return params;
                },
                "dataSrc": function(json) {
                    // Map the API response to the format DataTables expects
                    if (!json || !json.pagination) {
                        // Handle cases where the API returns an error
                        console.error("Invalid response from server:", json);
                        $('#login-section').removeClass('hidden');
                        $('#main-content').addClass('hidden');
                        return [];
                    }
                    return {
                        "draw": parseInt(json.pagination.current_page),
                        "recordsTotal": json.pagination.total_records,
                        "recordsFiltered": json.pagination.total_records, // The API doesn't provide a separate filtered count, so we use total.
                        "data": json.data
                    };
                },
                "error": function(xhr, error, thrown) {
                    // If the session expires or token is invalid, the proxy returns 401.
                    // Redirect to the login page by reloading.
                    if (xhr.status == 401) {
                        location.reload();
                    } else {
                        console.error("Error fetching data for table: ", error);
                    }
                }
            },
            "columns": [{
                "data": "id"
            }, {
                "data": "username"
            }, {
                "data": "email"
            }, {
                "data": "role_name"
            }],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });

        // Helper function to map DataTables column data name to API sort_by field name
        function mapColumnName(name) {
            const mapping = {
                'id': 'u.id',
                'username': 'u.username',
                'email': 'u.email',
                'role_name': 'r.role_name'
            };
            return mapping[name] || 'u.id';
        }
    }
});
