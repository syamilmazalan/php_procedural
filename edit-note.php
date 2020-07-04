<?php
require 'helpers.php';
require 'config/config.php';
require 'config/db.php';

session_start();

// Initialize variables
$title = $note = '';
$note_id = $_POST['note_id'];


// Check for submit
if (isset($_POST['edit'])) {
  // Sanitize input
  $title = sanitize_input($_POST['title']);
  $note = sanitize_input($_POST['note']);
  $note_id = sanitize_input($_POST['note_id']);

  // Update note in database
  $update_note_query = "UPDATE notes SET title = ?, note = ? WHERE id=?";

  if ($stmt = mysqli_prepare($conn, $update_note_query)) {
    mysqli_stmt_bind_param($stmt, 'ssi', $title, $note, $note_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }
}

// Redirect back to index and exit
header('Location: ' . ROOT_URL);
exit;