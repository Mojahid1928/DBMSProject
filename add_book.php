<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

$message = '';
$categories = [];

// Fetch categories from the database
try {
    $query = "SELECT cat_id, cat_name FROM category";
    $stmt = $conn->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = "Failed to fetch categories: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_FILES["bookimg"]) || $_FILES["bookimg"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("No valid file uploaded.");
        }

        $isbn = trim($_POST['isbn']);
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $description = trim($_POST['description']);
        $publisher = trim($_POST['publisher']);
        $cat_id = (int) $_POST['category'];

        $imgName = basename($_FILES["bookimg"]["name"]);
        $tempName = $_FILES["bookimg"]["tmp_name"];
        $uploadDir = "C:/xampp/htdocs/project1/assets/images/Books/";
        $uploadFile = $uploadDir . $imgName;

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Only JPG, JPEG, and PNG files are allowed.");
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($tempName, $uploadFile)) {
            throw new Exception("Failed to move uploaded file.");
        }

        $query = "INSERT INTO book (isbn, title, author, description, publisher, cover, cat_id) 
                  VALUES (:isbn, :title, :author, :description, :publisher, :cover, :cat_id)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            'isbn' => $isbn,
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'publisher' => $publisher,
            'cover' => $imgName,
            'cat_id' => $cat_id
        ]);

        $message = "Book added successfully!";
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../project1/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Add Book</title>
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
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2><i class="bi bi-book-fill"></i> Add a New Book</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-info text-center"><i class="bi bi-info-circle-fill"></i> <?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST" action="add_book.php" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="isbn" class="form-label"><i class="bi bi-upc-scan"></i> ISBN</label>
                        <input type="text" id="isbn" name="isbn" class="form-control" placeholder="Enter ISBN" required>
                        <div class="invalid-feedback">Please provide a valid ISBN.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="title" class="form-label"><i class="bi bi-bookmark"></i> Title</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="Enter Title" required>
                        <div class="invalid-feedback">Please provide the book title.</div>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label for="author" class="form-label"><i class="bi bi-pen"></i> Author</label>
                        <input type="text" id="author" name="author" class="form-control" placeholder="Enter Author" required>
                        <div class="invalid-feedback">Please provide the author's name.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="publisher" class="form-label"><i class="bi bi-building"></i> Publisher</label>
                        <input type="text" id="publisher" name="publisher" class="form-control" placeholder="Publisher Name" required>
                        <div class="invalid-feedback">Please provide the publisher's name.</div>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="description" class="form-label"><i class="bi bi-card-text"></i> Description</label>
                    <textarea id="description" name="description" rows="3" class="form-control" placeholder="Describe the Book" required></textarea>
                    <div class="invalid-feedback">Please provide a description of the book.</div>
                </div>
                <div class="mt-3">
                    <label for="quantity" class="form-label"><i class="bi bi-list-ol"></i> Quantity</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Enter Quantity" required>
                    <div class="invalid-feedback">Please provide a valid quantity.</div>
                </div>
                <div class="mt-3">
                    <label for="category" class="form-label"><i class="bi bi-tags-fill"></i> Category</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="" disabled selected>Select a Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['cat_id']; ?>"><?php echo htmlspecialchars($category['cat_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a category.</div>
                </div>
                <div class="mt-3">
                    <label for="img" class="form-label"><i class="bi bi-image"></i> Upload Book Image</label>
                    <input type="file" class="form-control" name="bookimg" id="img" accept=".jpg,.png,.jpeg" required>
                    <div class="invalid-feedback">Please upload a valid image file (JPG, JPEG, PNG).</div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Book</button>
                </div>
                <div class="d-grid mt-3">
                    <a href="update_books.php" class="btn btn-secondary"><i class="bi bi-eye-fill"></i> View Books</a>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="dashboard.php" class="btn btn-link"><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

<script src="../project1/assets/js/bootstrap.bundle.min.js"></script>
<script>
    // Bootstrap form validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
</body>
</html>
