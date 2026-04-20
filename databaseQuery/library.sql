CREATE DATABASE library_management;
USE library_management;

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    id_number VARCHAR(20) NOT NULL,
    is_admin BOOLEAN DEFAULT false,
    password VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE librarian(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE borrowers(
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100) NOT NULL,
    published_date TIMESTAMP NOT NULL,
    genre VARCHAR(25) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
    is_available BOOLEAN DEFAULT true;  
);

CREATE TABLE borrow_transaction(
    id INT AUTO_INCREMENT PRIMARY KEY,
    librarian_id INT NOT NULL,
    borrower_id INT NOT NULL,
    book_id INT NOT NULL,
    borrowed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    return_date DATETIME NOT NULL,
    status ENUM("borrowed", "returned"),
    FOREIGN KEY (librarian_id) REFERENCES librarian(id) ON delete CASCADE,
    FOREIGN KEY (borrower_id) REFERENCES borrowers(id) ON delete CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON delete CASCADE
);