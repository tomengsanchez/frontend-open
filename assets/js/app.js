$(document).ready(function() {

    // If the users-table exists on the page, initialize DataTables
    if ($('#users-table').length) {
        loadUsersTable();
    }

    // Handle Login Form Submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        const username = $('#username').val();
        const password = $('#password').val();
        const $loginError = $('#login-error');

        $.ajax({
            url: '/user/handleLogin', // MVC route
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                password: password
            }),
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect to the dashboard on successful login
                    window.location.href = '/dashboard';
                } else {
                    $loginError.text(response.message || 'An unknown error occurred.').removeClass('hidden');
                }
            },
            error: function(jqXHR) {
                let errorMessage = 'An error occurred during login.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                }
                $loginError.text(errorMessage).removeClass('hidden');
            }
        });
    });

    // Function to initialize and load the DataTable
    function loadUsersTable() {
        $('#users-table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "/user/usersApi", // MVC route for the API
                "type": "GET",
                "data": function(d) {
                    // Map DataTables parameters to what our API expects
                    const mapping = {
                        'id': 'u.id',
                        'username': 'u.username',
                        'email': 'u.email',
                        'role_name': 'r.role_name'
                    };
                    return {
                        per_page: d.length,
                        page: (d.start / d.length) + 1,
                        search: d.search.value,
                        sort_by: mapping[d.columns[d.order[0].column].data],
                        sort_direction: d.order[0].dir
                    };
                },
                "dataSrc": function(json) {
                    if (!json || !json.pagination) {
                        console.error("Invalid response from server:", json);
                        // If there's an auth error, redirect to login
                        window.location.href = '/user/login';
                        return [];
                    }
                    // Map the API response to the format DataTables expects
                    return {
                        "draw": parseInt(json.pagination.current_page),
                        "recordsTotal": json.pagination.total_records,
                        "recordsFiltered": json.pagination.total_records,
                        "data": json.data
                    };
                },
                "error": function(xhr) {
                    if (xhr.status == 401) {
                        window.location.href = '/user/login';
                    } else {
                        console.error("Error fetching data for table: ", xhr.responseText);
                    }
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "username" },
                { "data": "email" },
                { "data": "role_name" }
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
