<?php
session_start();
include 'db_connection.php';

if ($_SESSION['role'] !== 'librarian') {
    header("Location: dashboard.php");
    exit();
}

// Handle book management actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $isbn = $_POST['isbn'];

    if ($action === 'add') {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $description = $_POST['description'];
        $publisher = $_POST['publisher'];
        $quantity = $_POST['quantity'];
        $cat_id = $_POST['cat_id'];

        $query = "INSERT INTO book (isbn, title, author, description, publisher, quantity, cat_id) 
                  VALUES (:isbn, :title, :author, :description, :publisher, :quantity, :cat_id)";
        $stmt = $conn->prepare($query);
        $stmt->execute(compact('isbn', 'title', 'author', 'description', 'publisher', 'quantity', 'cat_id'));
    } elseif ($action === 'delete') {
        $query = "DELETE FROM book WHERE isbn = :isbn";
        $stmt = $conn->prepare($query);
        $stmt->execute(['isbn' => $isbn]);
    }
}

// Fetch all books
$books = $conn->query("SELECT * FROM book")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Book Management</title>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Book Management</h1>

        <div class="row">
            <!-- Add New Book Form - Basic Information (Column 1) -->
            <div class="col-md-6 mb-5">
                <h2>Add Basic Book Information</h2>
                <form method="POST" action="" class="mb-4">
                    <div class="form-group">
                        <input type="text" class="form-control" name="isbn" placeholder="ISBN" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="title" placeholder="Title" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="author" placeholder="Author">
                    </div>
                    <button type="submit" name="action" value="add_basic" class="btn btn-primary">Save Basic Info</button>
                </form>
            </div>

            <!-- Add New Book Form - Additional Information (Column 2) -->
            <div class="col-md-6 mb-5">
                <h2>Add Additional Book Information</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <textarea class="form-control" name="description" placeholder="Description"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="publisher" placeholder="Publisher">
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="quantity" placeholder="Quantity" required>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="cat_id" required>
                            <?php
                            $categories = $conn->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                echo "<option value='{$category['cat_id']}'>{$category['cat_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="action" value="add_additional" class="btn btn-success">Save Additional Info</button>
                </form>
            </div>
        </div>

        <!-- Existing Books Table -->
        <h2>Existing Books</h2>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>ISBN</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['quantity']); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a></p>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>


</html>
