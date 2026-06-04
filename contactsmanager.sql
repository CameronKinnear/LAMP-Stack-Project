CREATE DATABASE contactsmanager;
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
    UserID INT,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Phone VARCHAR(20),
    Email VARCHAR(50),

    FOREIGN KEY (UserID)
    REFERENCES Users(ID)
    ON DELETE CASCADE
);

CREATE USER 'DbEditor' identified by 'Lampgroup10';
GRANT ALL PRIVILEDGES ON contactsmanager.* to 'DbEditor'@'%';