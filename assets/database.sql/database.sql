-- Creating the librarian table
CREATE TABLE librarian (
    lib_id INT PRIMARY KEY AUTO_INCREMENT,
    lib_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(15),
    address VARCHAR(255),
    password VARCHAR(100) NOT NULL
);

-- Creating the student table
CREATE TABLE student (
    stud_id INT PRIMARY KEY AUTO_INCREMENT,
    stu_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mobile VARCHAR(15),
    course VARCHAR(50),
    address VARCHAR(255),
    date_of_birth DATE,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'student'
);

-- Creating the category table
CREATE TABLE category (
    cat_id INT PRIMARY KEY AUTO_INCREMENT,
    cat_name VARCHAR(100) NOT NULL
);

-- Creating the book table with the added 'status' column
CREATE TABLE book (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    isbn VARCHAR(13) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100),
    description TEXT,
    publisher VARCHAR(100),
    quantity INT NOT NULL,
    cover VARCHAR(255),
    cat_id INT,
    status ENUM('yes', 'no') DEFAULT 'yes', -- Added 'status' column
    FOREIGN KEY (cat_id) REFERENCES category(cat_id) ON DELETE SET NULL
);

-- Creating the borrowing table
CREATE TABLE borrowing (
    borrow_id INT PRIMARY KEY AUTO_INCREMENT,
    stud_id INT,
    book_id INT,
    lib_id INT,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status ENUM('borrowed', 'returned', 'requested') DEFAULT 'borrowed',
    FOREIGN KEY (stud_id) REFERENCES student(stud_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES book(book_id) ON DELETE CASCADE,
    FOREIGN KEY (lib_id) REFERENCES librarian(lib_id) ON DELETE SET NULL
);

-- Trigger to update book status to 'no' when a book is borrowed
DELIMITER $$

CREATE TRIGGER set_book_borrowed
AFTER INSERT ON borrowing
FOR EACH ROW
BEGIN
    UPDATE book
    SET status = 'no'
    WHERE book_id = NEW.book_id;
END$$

DELIMITER ;

-- Trigger to update book status to 'yes' when a book is returned
DELIMITER $$

CREATE TRIGGER set_book_returned
AFTER UPDATE ON borrowing
FOR EACH ROW
BEGIN
    IF NEW.status = 'returned' THEN
        UPDATE book
        SET status = 'yes'
        WHERE book_id = NEW.book_id;
    END IF;
END$$

DELIMITER ;
-- Inserting sample data into the librarian table
INSERT INTO librarian (lib_name, email, mobile, address, password) VALUES
('Alice Johnson', 'alice@example.com', '1234567890', '123 Main St, Cityville', 'password123'),
('Bob Smith', 'bob@example.com', '9876543210', '456 Elm St, Townsville', 'password456');

-- Inserting sample data into the student table
INSERT INTO student (stu_name, email, mobile, course, address, date_of_birth, password) VALUES
('John Doe', 'john.doe@example.com', '1112223333', 'Computer Science', '789 Pine St, Cityville', '2000-05-15', 'pass123'),
('Jane Smith', 'jane.smith@example.com', '4445556666', 'Engineering', '101 Maple Ave, Townsville', '1999-11-25', 'pass456');

-- Inserting sample data into the category table
INSERT INTO category (cat_name) VALUES
('Science Fiction'),
('Programming'),
('Mystery');

-- Inserting sample data into the book table
INSERT INTO book (isbn, title, author, description, publisher, quantity, cover, cat_id, status) VALUES
('9780451524935', '1984', 'George Orwell', 'A dystopian novel set in a totalitarian society.', 'Secker & Warburg', 5, 'cover_1984.jpg', 1, 'yes'),
('9780131103627', 'The C Programming Language', 'Brian W. Kernighan and Dennis M. Ritchie', 'Classic book for learning C programming.', 'Prentice Hall', 3, 'cover_c_programming.jpg', 2, 'yes'),
('9780307594006', 'Gone Girl', 'Gillian Flynn', 'A thrilling mystery novel.', 'Crown Publishing Group', 2, 'cover_gone_girl.jpg', 3, 'yes');

-- Inserting sample data into the borrowing table
INSERT INTO borrowing (stud_id, book_id, lib_id, issue_date, due_date, return_date, status) VALUES
(1, 1, 1, '2024-12-01', '2024-12-15', NULL, 'borrowed'),
(2, 3, 2, '2024-12-03', '2024-12-17', NULL, 'borrowed');
