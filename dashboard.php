<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'] ?? [];
$role = $_SESSION['role'] ?? '';
$userName = $user['stu_name'] ?? $user['lib_name'] ?? 'Guest';

// Initialize statistics
$totalBooks = $issuedBooks = $borrowedBooks = $toReturnBooks = $totalCategories = $totalStudents = 0;
$totalBooksQuery = "SELECT COUNT(*) AS total_books FROM book";
$issuedBooksQuery = "SELECT COUNT(*) AS issued_books FROM borrowing WHERE status = 'borrowed'";
$categoriesQuery = "SELECT COUNT(*) AS total_categories FROM category";
$studentsQuery = "SELECT COUNT(*) AS total_students FROM student";
$totalBooks = $conn->query($totalBooksQuery)->fetch(PDO::FETCH_ASSOC)['total_books'];
$issuedBooks = $conn->query($issuedBooksQuery)->fetch(PDO::FETCH_ASSOC)['issued_books'];
$totalCategories = $conn->query($categoriesQuery)->fetch(PDO::FETCH_ASSOC)['total_categories'];
$totalStudents = $conn->query($studentsQuery)->fetch(PDO::FETCH_ASSOC)['total_students'];

if ($role === 'student' && isset($user['stud_id'])) {
    $stud_id = $user['stud_id'];
    $borrowedBooksQuery = "SELECT COUNT(*) AS borrowed_books FROM borrowing WHERE stud_id = :stud_id AND status = 'borrowed'";
    
    $stmt = $conn->prepare($borrowedBooksQuery);
    $stmt->execute(['stud_id' => $stud_id]);
    $borrowedBooks = $stmt->fetch(PDO::FETCH_ASSOC)['borrowed_books'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Dashboard</title>
    <style>
        body {
            background: linear-gradient(135deg, #2efff1, #ff2ef8);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .card {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(45deg, #ff6b6b, #1e90ff);
            color: #fff;
            border-radius: 15px 15px 0 0;
        }
        .btn-primary {
            background: #1e90ff;
            border: none;
        }
        .btn-primary:hover
        {
            background: #ff6b6b;
        }
        a {
            color: #1e90ff;
        }
        a:hover {
            color: #ff6b6b;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="text-primary">Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
        </div>

        <!-- Display statistics -->
        <div class="row mb-4">
            <?php if ($role === 'librarian'): ?>
                <div class="col-md-3">
                    <div class="card bg-info text-white text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-bookshelf"></i> Total Books</h5>
                            <p class="display-6"><?php echo $totalBooks; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-arrow-repeat"></i> Issued Books</h5>
                            <p class="display-6"><?php echo $issuedBooks; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-tags"></i> Categories</h5>
                            <p class="display-6"><?php echo $totalCategories; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-person-fill"></i> Total Students</h5>
                            <p class="display-6"><?php echo $totalStudents; ?></p>
                        </div>
                    </div>
                </div>
            <?php elseif ($role === 'student'): ?>
                <div class="col-md-6">
                    <div class="card bg-info text-white text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-bookshelf"></i> Total Books</h5>
                            <p class="display-6"><?php echo $totalBooks; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-bag-check"></i> Borrowed Books</h5>
                            <p class="display-6"><?php echo $borrowedBooks; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Role-based actions -->
        <div class="row">
            <?php if ($role === 'student'): ?>
                <div class="col-md-6 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-book"></i> Borrow Books</h5>
                            <p>Explore available books in the library.</p>
                            <a href="borrow_book.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-clock-history"></i> Borrowing History</h5>
                            <p>View your borrowed books history.</p>
                            <a href="borrow_return.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            <?php elseif ($role === 'librarian'): ?>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-plus-square"></i> Add Books</h5>
                            <p>Add new books to the library.</p>
                            <a href="add_book.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-pencil-square"></i> Update Books</h5>
                            <p>Manage existing books in the library.</p>
                            <a href="update_books.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-tags"></i> Manage Categories</h5>
                            <p>Organize books by categories.</p>
                            <a href="category_management.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-bookmark-check"></i> Issued Books</h5>
                            <p>View all issued books.</p>
                            <a href="borrowed_books.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No role-specific actions available.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
