<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'library_management_system'; // Replace with your database name

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Random data generators
function randomString($length = 10) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function randomPhone() {
    return '9' . rand(100000000, 999999999);
}

function randomDate($startDate, $endDate) {
    $start = strtotime($startDate);
    $end = strtotime($endDate);
    $randomDate = mt_rand($start, $end);
    return date("Y-m-d", $randomDate);
}

// Insert into librarian table
for ($i = 1; $i <= 10; $i++) {
    $name = randomString(8);
    $email = strtolower($name) . "@library.com";
    $mobile = randomPhone();
    $address = randomString(20);
    $password = md5("password$i");

    $sql = "INSERT INTO librarian (lib_name, email, mobile, address, password) 
            VALUES ('$name', '$email', '$mobile', '$address', '$password')";
    $conn->query($sql);
}

// Insert into student table
for ($i = 1; $i <= 50; $i++) {
    $name = randomString(8);
    $email = strtolower($name) . "@student.com";
    $mobile = randomPhone();
    $course = "Course" . rand(1, 5);
    $address = randomString(20);
    $dob = randomDate("2000-01-01", "2010-12-31");
    $password = md5("password$i");

    $sql = "INSERT INTO student (stu_name, email, mobile, course, address, date_of_birth, password) 
            VALUES ('$name', '$email', '$mobile', '$course', '$address', '$dob', '$password')";
    $conn->query($sql);
}

// Insert into category table
$categories = ['Science', 'Math', 'Literature', 'History', 'Technology'];
foreach ($categories as $category) {
    $sql = "INSERT INTO category (cat_name) VALUES ('$category')";
    $conn->query($sql);
}

// Insert into book table
for ($i = 1; $i <= 100; $i++) {
    $isbn = rand(1000000000000, 9999999999999);
    $title = randomString(15);
    $author = randomString(10);
    $description = randomString(50);
    $publisher = randomString(10);
    $quantity = rand(1, 10);
    $cat_id = rand(1, count($categories));
    $status = rand(0, 1) ? 'yes' : 'no';

    $sql = "INSERT INTO book (isbn, title, author, description, publisher, quantity, cat_id, status) 
            VALUES ('$isbn', '$title', '$author', '$description', '$publisher', $quantity, $cat_id, '$status')";
    $conn->query($sql);
}

// Insert into borrowing table
for ($i = 1; $i <= 200; $i++) {
    $stud_id = rand(1, 50);
    $book_id = rand(1, 100);
    $lib_id = rand(1, 10);
    $issue_date = randomDate("2023-01-01", "2023-12-31");
    $due_date = date("Y-m-d", strtotime($issue_date . ' +7 days'));
    $return_date = rand(0, 1) ? date("Y-m-d", strtotime($issue_date . ' +'.rand(1, 14).' days')) : null;
    $status = $return_date ? 'returned' : 'borrowed';

    $sql = "INSERT INTO borrowing (stud_id, book_id, lib_id, issue_date, due_date, return_date, status) 
            VALUES ($stud_id, $book_id, $lib_id, '$issue_date', '$due_date', " . 
            ($return_date ? "'$return_date'" : "NULL") . ", '$status')";
    $conn->query($sql);
}

echo "Random data inserted successfully.";

$conn->close();
?>
