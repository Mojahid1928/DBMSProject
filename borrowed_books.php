<?php
session_start();
include 'db_connection.php';

// Check if the librarian is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'librarian') {
    header("Location: index.php");
    exit();
}

// Fetch borrowed and requested books
$sql = "SELECT b.borrow_id, b.book_id, bo.isbn, bo.title, s.stu_name, b.issue_date, b.due_date, b.status
FROM borrowing b
JOIN book bo ON b.book_id = bo.book_id
JOIN student s ON b.stud_id = s.stud_id
WHERE b.status IN ('borrowed', 'requested')";


$stmt = $conn->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
$lib_id = $_SESSION['user']['id'] ?? null;

// Handle returning a book
if (isset($_GET['return_id'])) {
    $borrow_id = $_GET['return_id'];

    // Update the status of the borrowed book to 'returned'
    $updateQuery = "UPDATE borrowing SET  status = 'returned', return_date = NOW() WHERE borrow_id = :borrow_id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute(['borrow_id' => $borrow_id]);

    header("Location: borrowed_books.php");
    exit();
}

// Handle issuing a book
if (isset($_GET['issue_id'])) {
    $borrow_id = $_GET['issue_id'];

    // Update the status of the requested book to 'borrowed'
    $issueQuery = "UPDATE borrowing 
                   SET status = 'borrowed', issue_date = CURDATE(), due_date = DATE_ADD(CURDATE(), INTERVAL 14 DAY) 
                   WHERE borrow_id = :borrow_id";
    $issueStmt = $conn->prepare($issueQuery);
    $issueStmt->execute(['borrow_id' => $borrow_id]);

    header("Location: borrowed_books.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Borrowed and Requested Books</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">📚 Borrowed and Requested Books</h1>

        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ISBN</th>
                    <th>Book Title</th>
                    <th>Student Name</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $index => $book): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['stu_name']); ?></td>
                            <td><?php echo htmlspecialchars($book['issue_date'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($book['due_date'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if ($book['status'] === 'borrowed'): ?>
                                    <span class="badge bg-warning text-dark">Borrowed</span>
                                <?php elseif ($book['status'] === 'requested'): ?>
                                    <span class="badge bg-info text-dark">Requested</span>
                                <?php elseif ($book['status'] === 'returned'): ?>
                                    <span class="badge bg-success">Returned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($book['status'] === 'borrowed'): ?>
                                    <a href="?return_id=<?php echo $book['borrow_id']; ?>" class="btn btn-sm btn-success">
                                        <i class="bi bi-arrow-return-left"></i> Mark as Returned
                                    </a>
                                <?php elseif ($book['status'] === 'requested'): ?>
                                    <a href="?issue_id=<?php echo $book['borrow_id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check-circle"></i> Issue Book
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
