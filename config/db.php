<?php

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
  echo ('Failed to connect to MySQL: ' . mysqli_connect_errno());
}