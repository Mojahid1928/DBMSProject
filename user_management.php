<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'librarian') {
    header("Location: librarian_login.php");
    exit();
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $role = $_POST['role'];
    $user_id = $_POST['user_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($role === 'student') {
        if ($action === 'add' && $name && $email) {
            $query = "INSERT INTO student (stu_name, email, mobile, password) 
                      VALUES (:name, :email, :mobile, :password)";
            $stmt = $conn->prepare($query);
            $stmt->execute(compact('name', 'email', 'mobile', 'password'));
        } elseif ($action === 'delete' && $user_id) {
            $query = "DELETE FROM student WHERE stud_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['user_id' => $user_id]);
        }
    } elseif ($role === 'librarian') {
        if ($action === 'add' && $name && $email) {
            $query = "INSERT INTO librarian (lib_name, email, mobile, password) 
                      VALUES (:name, :email, :mobile, :password)";
            $stmt = $conn->prepare($query);
            $stmt->execute(compact('name', 'email', 'mobile', 'password'));
        } elseif ($action === 'delete' && $user_id) {
            $query = "DELETE FROM librarian WHERE lib_id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['user_id' => $user_id]);
        }
    }
}

// Fetch all users
$students = $conn->query("SELECT * FROM student")->fetchAll(PDO::FETCH_ASSOC);
$librarians = $conn->query("SELECT * FROM librarian")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>User Management</title>
</head>
<body>
    <div class="management-container">
        <h1>User Management</h1>

        <form method="POST" action="">
            <h2>Add New User</h2>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="librarian">Librarian</option>
            </select>
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="mobile" placeholder="Mobile" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="action" value="add">Add User</button>
        </form>

        <h2>Student List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['stud_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['stu_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($student['stud_id']); ?>">
                            <input type="hidden" name="role" value="student">
                            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Librarian List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($librarians as $librarian): ?>
                <tr>
                    <td><?php echo htmlspecialchars($librarian['lib_id']); ?></td>
                    <td><?php echo htmlspecialchars($librarian['lib_name']); ?></td>
                    <td><?php echo htmlspecialchars($librarian['email']); ?></td>
                    <td><?php echo htmlspecialchars($librarian['mobile']); ?></td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($librarian['lib_id']); ?>">
                            <input type="hidden" name="role" value="librarian">
                            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </div>
</body>
</html>
