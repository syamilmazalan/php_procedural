<?php

require 'helpers.php';
require 'config/config.php';
require 'config/db.php';

// Redirect to home if user logged in
if (isset($_SESSION['loggedIn']) || ($_SESSION['loggedIn'] === true)) {
  header('Location: ' . ROOT_URL);
  exit;
}

// Initialize variables
$name = $email = $password = "";
$nameErr = $emailErr = $passwordErr = "";

// Check for registration submit 
if (isset($_POST['register'])) {
  // Sanitize and validate input
  if (empty($_POST['name'])) {
    $nameErr = 'Name is required';
  } else {
    $name = sanitize_input($_POST['name']);
    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
      $nameErr = 'Only letters and white space allowed';
    }
  }
  if (empty($_POST['email'])) {
    $emailErr = 'Email is required';
  } else {
    $email = sanitize_input($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = 'Please enter a valid email';
    }
  }
  if (empty($_POST['password'])) {
    $passwordErr = 'Password is required';
  } else {
    $password = sanitize_input($_POST['password']);
    if (strlen($password) < 6) {
      $passwordErr = 'Password must be at least 6 characters';
    } else {
      $password = password_hash($password, PASSWORD_DEFAULT);
    }
  }

  // Run queries if no errors
  if (($nameErr === '' && $emailErr === '' && $passwordErr === '')) {
    $user_check_query = "SELECT email FROM users WHERE email=?";
    $insert_query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";

    // Check if email already taken
    if ($stmt = mysqli_prepare($conn, $user_check_query)) {
      mysqli_stmt_bind_param($stmt, 's', $email);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $responseEmail);
      mysqli_stmt_fetch($stmt);
      mysqli_stmt_close($stmt);

      if ($responseEmail) {
        $emailErr = 'That email is already in use.';
      } else {
        // Insert registered user into db
        if ($stmt = mysqli_prepare($conn, $insert_query)) {
          mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $password);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);

          // Redirect to Dashboard
          header('Location: ' . ROOT_URL);
        }
      }
    }
  }

  // Close connection
  mysqli_close($conn);
}

?>

<?php include 'includes/head.php'; ?>
<title>Notes - Register</title>
</head>

<body>
  <main class="container content">
    <div class="row h-100 align-items-center">
      <div class="col">
        <div class="card align-items-center w-80 py-5">
          <div class="card-body w-50">
            <h3 class="card-title">Register to Notes</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                  method="POST">
              <div class="form-group">
                <label for="name" class="mb-0 mr-2">Name: </label>
                <input type="text" name="name" id="name" class="form-control"
                       value="<?php echo $name; ?>">
                <span class="text-danger">* <?php echo $nameErr; ?></span>
              </div>
              <div class="form-group">
                <label for="email" class="mb-0 mr-2">Email: </label>
                <input type="text" name="email" id="email" class="form-control"
                       value="<?php echo $email; ?>">
                <span class="text-danger">* <?php echo $emailErr; ?></span>
              </div>
              <div class="form-group">
                <label for="password" class="mb-0 mr-2">Password: </label>
                <input type="password" name="password" id="password"
                       class="form-control">
                <span class="text-danger">* <?php echo $passwordErr; ?></span>
              </div>
              <input type="submit" name="register" value="Register"
                     class="btn btn-primary">
            </form>
            <a href="login.php">Login</a>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>