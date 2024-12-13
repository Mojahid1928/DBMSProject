<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, rgba(123, 46, 255, 0), rgba(224, 46, 255, 0)), 
                        url('../project1/assets/images/012.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Arial', sans-serif;
            color: white;
        }

        .main {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            max-width: 500px;
            width: 100%;
        }

        .main h1 {
            font-weight: bold;
            color: #5a5a5a;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2a5298, #1e3c72);
        }

        .btn-primary i {
            font-size: 20px;
        }

        .main p {
            color: #333;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="main text-center">
        <h1><i class="bi bi-book-fill"></i> Welcome to</h1>
        <h1>Library Management System</h1>
        <p class="mt-3">Please login to continue.</p>
        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="login.php" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
