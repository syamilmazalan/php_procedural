<?php
require 'helpers.php';
require 'config/config.php';
require 'config/db.php';

session_start();

// Set header to accept JSON
header('Content-Type: application/json, charset=UTF-8');
$payload = file_get_contents('php://input');

// Get ID from JSON
$noteId = (int) json_decode($payload);

// Delete note
$delete_query = "DELETE FROM notes WHERE id = ?";

if ($stmt = mysqli_prepare($conn, $delete_query)) {
  mysqli_stmt_bind_param($stmt, 'i', $noteId);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

mysqli_close($conn);

// Redirect and exit
header('Location: ' . ROOT_URL);
exit;