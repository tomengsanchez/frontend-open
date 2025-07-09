<?php
// debug_session.php

// Start the session to access its data
session_start();

// Set the content type to plain text for easy reading
header('Content-Type: text/plain');

echo "--- Current Session Data ---\n\n";

// Print all the data stored in the current session
print_r($_SESSION);

?>
