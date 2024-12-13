<?php
session_start();
include 'db_connection.php';

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$borrowings = []; // Initialize borrowings array

try {
    // Handle borrowing and returning actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        $book_id = $_POST['book_id'];
        $stud_id = $_SESSION['user']['stud_id'] ?? null;
        $lib_id = $_SESSION['user']['id'] ?? null;

        if ($action === 'borrow' && $role === 'student' && $stud_id) {
            $issue_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+14 days'));

            $query = "INSERT INTO borrowing (stud_id, book_id, lib_id, issue_date, due_date, status)
                      VALUES (:stud_id, :book_id, :lib_id, :issue_date, :due_date, 'borrowed')";
            $stmt = $conn->prepare($query);
            $stmt->execute(compact('stud_id', 'book_id', 'lib_id', 'issue_date', 'due_date'));
        } elseif ($action === 'return' && $role === 'librarian') {
            $return_date = date('Y-m-d');

            $query = "UPDATE borrowing SET return_date = :return_date, status = 'returned' 
                      WHERE book_id = :book_id AND stud_id = :stud_id AND status = 'borrowed'";
            $stmt = $conn->prepare($query);
            $stmt->execute(compact('return_date', 'book_id', 'stud_id'));
        }
    }

    // Fetch borrowing records based on role
    if ($role === 'student' && isset($_SESSION['user']['stud_id'])) {
        $stud_id = $_SESSION['user']['stud_id'];
        $query = "SELECT b.book_id, b.title, br.issue_date, br.due_date, br.return_date, br.status
                  FROM borrowing br
                  JOIN book b ON br.book_id = b.book_id
                  WHERE br.stud_id = :stud_id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['stud_id' => $stud_id]);
        $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($role === 'librarian') {
        $query = "SELECT br.stud_id, b.book_id, b.title, br.issue_date, br.due_date, br.return_date, br.status
                  FROM borrowing br
                  JOIN book b ON br.book_id = b.book_id";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Handle database errors gracefully
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowing History</title>
    <!-- Bootstrap 4/5 CSS for styling -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Borrowing History</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h2>Borrowing Records</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <?php if ($role === 'librarian'): ?><th>Student ID</th><?php endif; ?>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <?php if ($role === 'librarian'): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($borrowings)): ?>
                    <?php foreach ($borrowings as $borrowing): ?>
                        <tr>
                            <?php if ($role === 'librarian'): ?>
                                <td><?php echo htmlspecialchars($borrowing['stud_id']); ?></td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($borrowing['book_id']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['title']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['issue_date']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['due_date']); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['return_date'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($borrowing['status']); ?></td>
                            <?php if ($role === 'librarian' && $borrowing['status'] === 'borrowed'): ?>
                                <td>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($borrowing['book_id']); ?>">
                                        <input type="hidden" name="stud_id" value="<?php echo htmlspecialchars($borrowing['stud_id']); ?>">
                                        <button type="submit" name="action" value="return" class="btn btn-success btn-sm">Mark as Returned</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No borrowing records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p><a href="dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a></p>
    </div>

    <script src="../project2/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
