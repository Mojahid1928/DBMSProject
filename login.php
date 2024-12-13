<?php
// index.php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'student') {
        $query = "SELECT * FROM student WHERE email = :email AND password = :password";
    } elseif ($role === 'librarian') {
        $query = "SELECT * FROM librarian WHERE email = :email AND password = :password";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user'] = $user;
        $_SESSION['role'] = $role;
        if ($role === 'student') {
            $_SESSION['stud_id'] = $user['id']; // Use the correct key for student ID
        }
        if ($role === 'library') {
            $_SESSION['lib_id'] = $user['id']; // Use the correct key for librarian ID
        }
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-header py-4">
                        <h3><i class="bi bi-box-arrow-in-right me-2"></i>Library Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label"><i class="bi bi-envelope-fill"></i> Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><i class="bi bi-lock-fill"></i> Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label"><i class="bi bi-person-lines-fill"></i> Role</label>
                                <select id="role" name="role" class="form-select" required>
                                    <option value="student">Student</option>
                                    <option value="librarian">Librarian</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-arrow-right-circle-fill"></i> Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Not signed up? <a href="student_sign_up.php" class="text-primary text-decoration-none">Student Signup</a> | <a href="librarian_sign_up.php" class="text-primary text-decoration-none">Librarian Signup</a></p>
                        <p><a href="index.php" class="btn btn-primary"><i class="bi bi-arrow-bar-left"></i> Back to index</a></p>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
