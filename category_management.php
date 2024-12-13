<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'librarian') {
    header("Location: index.php");
    exit();
}

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $cat_id = $_POST['cat_id'] ?? null;
    $cat_name = $_POST['cat_name'] ?? '';

    if ($action === 'add' && !empty($cat_name)) {
        // Add category
        $query = "INSERT INTO category (cat_name) VALUES (:cat_name)";
        $stmt = $conn->prepare($query);
        $stmt->execute(['cat_name' => $cat_name]);
    } elseif ($action === 'update' && $cat_id && !empty($cat_name)) {
        // Update category
        $query = "UPDATE category SET cat_name = :cat_name WHERE cat_id = :cat_id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['cat_name' => $cat_name, 'cat_id' => $cat_id]);
    } elseif ($action === 'delete' && $cat_id) {
// Step 1: Delete rows in borrowing table associated with the books in this category
$query_borrowing = "DELETE FROM borrowing WHERE book_id IN (SELECT book_id FROM book WHERE cat_id = :cat_id)";
$stmt_borrowing = $conn->prepare($query_borrowing);
$stmt_borrowing->execute(['cat_id' => $cat_id]);

// Step 2: Delete books in the category
$query_books = "DELETE FROM book WHERE cat_id = :cat_id";
$stmt_books = $conn->prepare($query_books);
$stmt_books->execute(['cat_id' => $cat_id]);

// Step 3: Now delete the category
$query_category = "DELETE FROM category WHERE cat_id = :cat_id";
$stmt_category = $conn->prepare($query_category);
$stmt_category->execute(['cat_id' => $cat_id]);

    }
}

// Fetch all categories
$query = "SELECT * FROM category";
$stmt = $conn->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Category Management</title>
    <style>
        body {
            background: linear-gradient(135deg, #2efff1, #ff2ef8); /* Cool blue tones */

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
        .form-label {
            font-weight: bold;
            color: #555;
        }
        .btn-primary {
            background: #1e90ff;
            border: none;
        }
        .btn-primary:hover {
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
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1 class="text-primary"><i class="bi bi-tags-fill"></i> Category Management</h1>
        </div>

        <!-- Add New Category Form -->
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4><i class="bi bi-plus-circle"></i> Add New Category</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="cat_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="cat_name" name="cat_name" placeholder="Enter category name" required>
                    </div>
                    <button type="submit" name="action" value="add" class="btn btn-success"><i class="bi bi-check-circle"></i> Add Category</button>
                </form>
            </div>
        </div>

        <!-- Existing Categories Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h4><i class="bi bi-list-ul"></i> Existing Categories</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['cat_id']); ?></td>
                            <td><?php echo htmlspecialchars($category['cat_name']); ?></td>
                            <td>
                                <!-- Update Category Form -->
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="cat_id" value="<?php echo htmlspecialchars($category['cat_id']); ?>">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="cat_name" placeholder="New Name" required>
                                        <button type="submit" name="action" value="update" class="btn btn-warning"><i class="bi bi-pencil-square"></i></button>
                                    </div>
                                </form>
                                <!-- Delete Category Form -->
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="cat_id" value="<?php echo htmlspecialchars($category['cat_id']); ?>">
                                    <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm mt-1" onclick="return confirm('Are you sure you want to delete this category and all associated books?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="dashboard.php" class="btn btn-primary"><i class="bi bi-arrow-left-circle"></i> Back to Home</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
