<?php
require 'helpers.php';
require 'config/config.php';
require 'config/db.php';

// Start a session
session_start();

// Redirect to home if user logged in
if (isset($_SESSION['loggedIn']) || ($_SESSION['loggedIn'] === true)) {
  header('Location: ' . ROOT_URL);
  exit;
}

// Initialize variables
$email = $password = "";
$emailErr = $passwordErr = "";

// Sanitize and validate input
if (isset($_POST['login'])) {
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
  }

  // Run queries if no errors
  if (($emailErr === '' && $passwordErr === '')) {
    $query = "SELECT id, name, email, password FROM users WHERE email=?";

    // Get associated email and password for the given email
    if ($stmt = mysqli_prepare($conn, $query)) {
      mysqli_stmt_bind_param($stmt, 's', $email);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $id, $name, $responseEmail, $responsePassword);
      $result = mysqli_stmt_fetch($stmt);

      // Verify email exists
      if ($result) {
        // Verify password match
        if (!password_verify($password, $responsePassword)) {
          $passwordErr = "Invalid password";
        } else {
          // Place verified credentials in session
          $_SESSION['loggedIn'] = true;
          $_SESSION['id'] = $id;
          $_SESSION['name'] = $name;
          $_SESSION['email'] = $email;

          // Redirect to dashboard
          header('Location: ' . ROOT_URL);
        }
      } else {
        $emailErr = 'Email not found';
      }

      mysqli_stmt_close($stmt); // Will cause an exception in XDebug
    } else {
      echo 'Error';
    }
  }
  mysqli_close($conn); // Will cause an exception in XDebug
}

?>

<?php include 'includes/head.php'; ?>
<title>Notes - Login</title>
</head>

<body>
  <main class="container content">
    <div class="row h-100 align-items-center">
      <div class="col">
        <div class="card align-items-center w-80 py-5">
          <div class="card-body">
            <h3>Login to Notes</h3>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                  method="POST">

              <div class="form-group">
                <label for="email"
                       class="mb-0 mr-2">Email: </label>
                <input type="text"
                       name="email"
                       id="email"
                       class="form-control"
                       value="<?php echo $email; ?>">
                <span class="text-danger"><?php echo $emailErr; ?></span>
              </div>

              <div class="form-group">
                <label for="password"
                       class="mb-0 mr-2">Password: </label>
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control">
                <span class="text-danger"><?php echo $passwordErr; ?></span>
              </div>
              <input type="submit"
                     name="login"
                     value="Login"
                     class="btn btn-primary">
            </form>
            <a href="register.php">Register</a>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>