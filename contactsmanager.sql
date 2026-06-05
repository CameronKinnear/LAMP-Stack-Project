CREATE DATABASE IF NOT EXISTS contactsmanager;
USE contactsmanager;

CREATE TABLE Users (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Login VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(300) NOT NULL
);

CREATE TABLE Contacts (
    ContactID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Phone VARCHAR(20),
    Email VARCHAR(50),

    FOREIGN KEY (UserID)
    REFERENCES Users(ID)
    ON DELETE CASCADE
);

-- Database users and passwords should not be committed to GitHub.
-- Each deployment should create its own database user privately.
--
-- Example only:
-- CREATE USER 'your_database_user'@'localhost' IDENTIFIED BY 'your_strong_password';
-- GRANT ALL PRIVILEGES ON contactsmanager.* TO 'your_database_user'@'localhost';
-- FLUSH PRIVILEGES;