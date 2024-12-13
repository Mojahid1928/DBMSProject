<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$stud_id = $_SESSION['user']['stud_id'] ?? null; // Assuming the logged-in user's ID is stored in the session
$role = $_SESSION['role']; // Assuming the role is also stored in the session

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search functionality
$search_isbn = isset($_GET['search_isbn']) ? trim($_GET['search_isbn']) : null;
$where_clause = $search_isbn ? "WHERE isbn = :isbn" : "";

// Fetch books
$query = "SELECT * FROM book $where_clause LIMIT :start, :limit";
$stmt = $conn->prepare($query);
if ($search_isbn) {
    $stmt->bindValue(':isbn', $search_isbn, PDO::PARAM_STR);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total books
$total_query = "SELECT COUNT(*) as total FROM book";
$total_stmt = $conn->prepare($total_query);
$total_stmt->execute();
$total_books = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_books / $limit);

// Handle book request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // Check if the book is already requested or borrowed by the same user
    $check_query = "SELECT * FROM borrowing WHERE book_id = :book_id AND stud_id = :user_id AND status IN ('requested', 'borrowed')";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->execute([':book_id' => $book_id, ':user_id' => $stud_id]);

    if ($check_stmt->rowCount() > 0) {
        $message = "You have already requested or borrowed this book.";
    } else {
        // Insert a new borrowing record with status 'requested'
        $insert_query = "INSERT INTO borrowing (stud_id, book_id, status) VALUES (:user_id, :book_id, 'requested')";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->execute([':user_id' => $stud_id, ':book_id' => $book_id]);
        $message = "Book successfully requested!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>Book List</title>

</head>

<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-center"><i class="bi bi-book"></i> Books Available</h1>
            <form class="d-flex" method="GET">
                <input type="text" name="search_isbn" class="form-control me-2" placeholder="Search by ISBN" value="<?= htmlspecialchars($search_isbn) ?>">
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> Search</button>
            </form>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><i class="bi bi-check-circle"></i> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (count($books) > 0): ?>
            <table class="table table-bordered table-striped align-middle shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Cover</th>
                        <th>ID</th>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr id="book-<?= htmlspecialchars($book['isbn']) ?>">
                            <td>
                                <img src="../assets/images/Books/<?= htmlspecialchars($book['cover']) ?>" alt="Cover Image" class="img-thumbnail" style="max-width: 100px;">
                            </td>
                            <td><?= htmlspecialchars($book['book_id']) ?></td>
                            <td><?= htmlspecialchars($book['isbn']) ?></td>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['description']) ?></td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="book_id" value="<?= htmlspecialchars($book['book_id']) ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-cart-plus"></i> Request
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning"><i class="bi bi-exclamation-circle"></i> No books available.</div>
        <?php endif; ?>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="bi bi-arrow-left"></i> Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next <i class="bi bi-arrow-right"></i></a>
                </li>
            </ul>
        </nav>

        <p><a href="dashboard.php" class="btn btn-primary"><i class="bi bi-arrow-bar-left"></i> Back to Home</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>