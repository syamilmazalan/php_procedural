<?php
require 'helpers.php';
require 'config/config.php';
require 'config/db.php';

session_start();

// Initialize variables
$title = $note = '';
$user_id = $_SESSION['id'];

// Check for submit
if (isset($_POST['create'])) {
  // Sanitize input
  $title = sanitize_input($_POST['title']);
  $note = sanitize_input($_POST['note']);

  // Add note to database
  // Define query
  $add_note_query = "INSERT INTO notes (user_id, title, note) VALUES (?, ?, ?)";

  // Prepare statement
  if ($stmt = mysqli_prepare($conn, $add_note_query)) {
    // Bind params
    mysqli_stmt_bind_param($stmt, 'iss', $user_id, $title, $note);
    // Execute statement
    mysqli_stmt_execute($stmt);
    // Close statement
    mysqli_stmt_close($stmt);
  }
}

// Redirect to index
header('Location: ' . ROOT_URL);

exit;