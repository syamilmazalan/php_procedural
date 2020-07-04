<?php
require 'helpers.php';
require 'config/config.php';
require 'config/db.php';

// Initialize session
session_start();

// Redirect to login if user not logged in
if (!isset($_SESSION['loggedIn']) || !($_SESSION['loggedIn'] === true)) {
  header('Location: ' . ROOT_URL . 'login.php');
  exit;
}

// Get current user email
$email = $_SESSION['email'];

// Get current page no
if (isset($_GET['pageno'])) {
  $pageno = $_GET['pageno'];
} else {
  $pageno = 1;
}

// Set pagination offset
$no_of_notes_per_page = 6;
$offset = ($pageno - 1) * $no_of_notes_per_page;

// Get user id
$user_query = "SELECT id FROM users WHERE email=?";
if ($stmt = mysqli_prepare($conn, $user_query)) {
  mysqli_stmt_bind_param($stmt, 's', $email);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $user_id);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_free_result($stmt);
  // mysqli_stmt_close($stmt);
}

// Get notes
$note_query = "SELECT id, title, note FROM notes WHERE user_id=? LIMIT $offset, $no_of_notes_per_page";
if ($stmt = mysqli_prepare($conn, $note_query)) {
  mysqli_stmt_bind_param($stmt, 'i', $user_id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $notes[$row['id']]['id'] = $row['id'];
    $notes[$row['id']]['title'] = $row['title'];
    $notes[$row['id']]['note'] = $row['note'];
  }
  // mysqli_stmt_close($stmt);
};

// Total pages query
$total_pages_query = "SELECT COUNT(*) FROM notes";
if ($stmt = mysqli_prepare($conn, $total_pages_query)) {
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $total_rows = mysqli_fetch_array($result)[0];
  $total_pages = ceil($total_rows / $no_of_notes_per_page);
}

// mysqli_close($conn);
?>

<?php include 'includes/head.php'; ?>
<title>Notes - Dashboard</title>
</head>
<!-- Procedural PHP Project -->

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <a href="<?php echo ROOT_URL; ?>"
         class="navbar-brand">Notes</a>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
          <a href="#"
             class="nav-link dropdown-toggle"
             id="userMenuLink"
             role="button"
             data-toggle="dropdown"
             aria-haspopup="true"
             aria-expanded="false">
            <?php echo $_SESSION['name']; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right"
               aria-labelledby="userMenuLink">
            <a href="logout.php"
               class="dropdown-item">Logout</a>
          </div>
        </li>
      </ul>
    </div>
  </nav>
  <main class="container content">
    <div class="row h-75 align-items-center">
      <div class="col">
        <div class="card w-80 p-3 shadow-sm">

          <div class="row">
            <div class="col-6">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h3 class="mb-3">Note</h3>
                  <form action="add-note.php"
                        method="POST"
                        id="note-form">
                    <div class="form-group">
                      <label for="title">Note Title:</label>
                      <input type="text"
                             name="title"
                             id="title"
                             class="form-control">
                    </div>
                    <div class="form-group">
                      <label for="note">Enter Note:</label>
                      <textarea name="note"
                                id="note"
                                class="form-control"
                                rows="10"></textarea>
                    </div>

                    <button type="submit"
                            name="create"
                            class="btn btn-block btn-primary"
                            id="submit">Add Note</button>

                  </form>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h3 class="mb-3">Note List</h3>
                  <ul class="list-group">
                    <?php foreach ($notes as $note) : ?>
                    <li class="list-group-item notes"
                        id="<?php echo $note['id']; ?>">
                      <?php echo $note['title']; ?>
                      <button type="button"
                              class="close"
                              aria-label="Close">
                        <span aria-hidden="true">&times;
                        </span>
                      </button>
                    </li>
                    <?php endforeach; ?>
                    <button
                            class="list-group-item list-group-item-action list-group-item-success">Add
                      New Note</button>
                  </ul>
                  <nav class="mt-3"
                       aria-label="Notes page navigation">
                    <ul class="pagination justify-content-center">
                      <li class="page-item <?php if ($pageno <= 1) {
                                              echo 'disabled';
                                            } ?>">
                        <a href="<?php if ($pageno <= 1) {
                                    echo '#';
                                  } else {
                                    echo "?pageno=" . ($pageno - 1);
                                  } ?>"
                           class="page-link"
                           aria-label="Previous">
                          <span aria-hidden="true">&laquo;</span>
                        </a>
                      </li>
                      <?php for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item'>";
                        echo "<a href=?pageno=" . $i . ' ';
                        echo "class='page-link'>$i</a>";
                        echo "</li>";
                      } ?>
                      <li class="page-item <?php if ($pageno >= $total_pages) {
                                              echo 'disabled';
                                            } ?>">
                        <a href="<?php if ($pageno >= $total_pages) {
                                    echo '#';
                                  } else {
                                    echo "?pageno=" . ($pageno + 1);
                                  } ?>"
                           class="page-link"
                           aria-label="Next">
                          <span aria-hidden="true">&raquo;</span>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
  <script src="assets/js/app.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
          integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
          crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
          integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
          crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
          integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
          crossorigin="anonymous"></script>

</body>

</html>