<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'student_lab_db';

try {
    // Create connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// SQL to create database and table if they don't exist
$createDB = "CREATE DATABASE IF NOT EXISTS student_lab_db";
$createTable = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    course VARCHAR(100) NOT NULL,
    year_level ENUM('1st Year', '2nd Year', '3rd Year', '4th Year') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

try {
    // Create database
    $tempPdo = new PDO("mysql:host=$host", $username, $password);
    $tempPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $tempPdo->exec($createDB);
    
    // Create table
    $pdo->exec($createTable);
    
} catch(PDOException $e) {
    echo "Error creating database/table: " . $e->getMessage();
}
?>