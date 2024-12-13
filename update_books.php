<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the AJAX request to delete a book
    $data = json_decode(file_get_contents('php://input'), true);
    $book_id = $data['book_id'];

    try {
        // Delete from borrowing table
        $delete_borrowing = $conn->prepare("DELETE FROM borrowing WHERE book_id = :book_id");
        $delete_borrowing->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $delete_borrowing->execute();

        // Delete from book table
        $delete_book = $conn->prepare("DELETE FROM book WHERE book_id = :book_id");
        $delete_book->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $delete_book->execute();

        echo json_encode(['success' => true]);
        exit();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}

if ($role = 'librarian') {
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

    <a href="add_book.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Add New Book</a>

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
                    <tr id="book-<?= htmlspecialchars($book['book_id']) ?>">
                        <td>
                            <img src="../assets/images/Books/<?= htmlspecialchars($book['cover']) ?>" alt="Cover Image" class="img-thumbnail" style="max-width: 100px;">
                        </td>
                        <td><?= htmlspecialchars($book['book_id']) ?></td>
                        <td><?= htmlspecialchars($book['isbn']) ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['description']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm remove-btn" data-book-id="<?= htmlspecialchars($book['book_id']) ?>">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                            <a href="update_book.php?book_id=<?= htmlspecialchars($book['book_id']) ?>" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square mt-3"></i> Update
                            </a>
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

    <p><a href="dashboard.php" class="btn btn-primary"><i class="bi bi-arrow-bar-left"></i> Back to Dashboard</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function () {
            const bookId = this.getAttribute('data-book-id');
            if (confirm('Are you sure you want to delete this book?')) {
                axios.post('', { book_id: bookId })
                    .then(response => {
                        if (response.data.success) {
                            document.getElementById(`book-${bookId}`).remove();
                            alert('Book removed successfully.');
                        } else {
                            alert('Failed to remove book: ' + response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert('An error occurred.');
                    });
            }
        });
    });
</script>
</body>
</html>
