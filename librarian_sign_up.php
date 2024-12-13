<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $query = "INSERT INTO librarian (lib_name, email, mobile, address, date_of_birth, password) 
              VALUES (:name, :email, :mobile, :address, :dob, :password)";
    $stmt = $conn->prepare($query);

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':mobile', $mobile);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':dob', $dob);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        header("Location: login.php"); // Redirect to login page
        exit();
    } else {
        $error = "Error occurred. Please try again!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Sign Up</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e90ff, #ff6b6b);
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
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <h3><i class="bi bi-person-badge-fill me-2"></i>Librarian Sign Up</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label"><i class="bi bi-person-fill"></i> Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label"><i class="bi bi-envelope-fill"></i> Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label"><i class="bi bi-telephone-fill"></i> Mobile Number</label>
                                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Enter your mobile number" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label"><i class="bi bi-geo-alt-fill"></i> Address</label>
                                <textarea id="address" name="address" class="form-control" placeholder="Enter your address" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label"><i class="bi bi-calendar-fill"></i> Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><i class="bi bi-lock-fill"></i> Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-plus-fill"></i> Sign Up</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Already have an account? <a href="index.php" class="text-decoration-none">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
