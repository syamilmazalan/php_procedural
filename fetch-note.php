<?php
require 'helpers.php';
require 'config/config.php';
require 'config/db.php';

$title = $_REQUEST['title'];

$note_query = "SELECT id, note FROM notes WHERE title=?";

// Get note
if ($stmt = mysqli_prepare($conn, $note_query)) {
  mysqli_stmt_bind_param($stmt, 's', $title);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $id, $note);
  mysqli_stmt_fetch($stmt);

  $result = [
    'id' => $id,
    'note' => $note
  ];
  // Return result
  echo (json_encode($result));

  // mysqli_stmt_close($stmt);
};