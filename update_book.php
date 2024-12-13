<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;

if ($book_id <= 0) {
    header("Location: update_books.php");
    exit();
}

$message = "";

try {
    $category_stmt = $conn->prepare("SELECT * FROM category");
    $category_stmt->execute();
    $categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = "Error fetching categories: " . $e->getMessage();
}


try {
    // Fetch book details
    $stmt = $conn->prepare("SELECT * FROM book WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        header("Location: update_books.php");
        exit();
    }
} catch (Exception $e) {
    $message = "Error fetching book details: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = $_POST['isbn'] ?? '';
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $publisher = $_POST['publisher'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = (int)($_POST['category'] ?? 0);

    try {
        // Update book details
        $update_stmt = $conn->prepare("
            UPDATE book 
            SET 
                isbn = :isbn, 
                title = :title, 
                author = :author, 
                publisher = :publisher, 
                description = :description,
                cat_id = :category 
            WHERE 
                book_id = :book_id
        ");

        $update_stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $update_stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $update_stmt->bindParam(':author', $author, PDO::PARAM_STR);
        $update_stmt->bindParam(':publisher', $publisher, PDO::PARAM_STR);
        $update_stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $update_stmt->bindParam(':category', $category, PDO::PARAM_INT);
        $update_stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);

        $update_stmt->execute();

        $message = "Book updated successfully!";
    } catch (Exception $e) {
        $message = "Error updating book: " . $e->getMessage();
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
    <title>Update Book</title>
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
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h2><i class="bi bi-pencil-square"></i> Update Book</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="isbn" class="form-label"><i class="bi bi-upc-scan"></i> ISBN</label>
                            <input type="text" id="isbn" name="isbn" class="form-control" value="<?= htmlspecialchars($book['isbn']) ?>" required>
                            <div class="invalid-feedback">Please provide a valid ISBN.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="title" class="form-label"><i class="bi bi-bookmark"></i> Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>" required>
                            <div class="invalid-feedback">Please provide the book title.</div>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="author" class="form-label"><i class="bi bi-pen"></i> Author</label>
                            <input type="text" id="author" name="author" class="form-control" value="<?= htmlspecialchars($book['author']) ?>" required>
                            <div class="invalid-feedback">Please provide the author's name.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="publisher" class="form-label"><i class="bi bi-building"></i> Publisher</label>
                            <input type="text" id="publisher" name="publisher" class="form-control" value="<?= htmlspecialchars($book['publisher']) ?>" required>
                            <div class="invalid-feedback">Please provide the publisher's name.</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="description" class="form-label"><i class="bi bi-card-text"></i> Description</label>
                        <textarea id="description" name="description" rows="3" class="form-control" required><?= htmlspecialchars($book['description']) ?></textarea>
                        <div class="invalid-feedback">Please provide a description of the book.</div>
                    </div>

                    <div class="mt-3">
                        <label for="category" class="form-label"><i class="bi bi-tags-fill"></i> Category</label>
                        <select id="category" name="category" class="form-select" required>
                            <option value="" disabled>Select a Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['cat_id']) ?>" <?= $category['cat_id'] == $book['cat_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['cat_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a category.</div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                    </div>
                    <div class="d-grid mt-3">
                        <a href="update_books.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../project1/assets/js/bootstrap.bundle.min.js"></script>
    <script>
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