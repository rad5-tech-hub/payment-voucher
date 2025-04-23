<?php
$host = 'localhost';
$db = '';
$user = '';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$domain = "";

// EMAIL SETTINGS
$host = "";
$username = "";
$password = "";

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

/*
CREATE TABLE vouchers ( 
      id INT AUTO_INCREMENT PRIMARY KEY, 
      voucher_id VARCHAR(100) UNIQUE, 
      payee VARCHAR(255), 
      address TEXT, 
      cheque_no VARCHAR(255) NULL, 
      payment_type VARCHAR(255), 
      amount_words VARCHAR(255), 
      prepared_by_name VARCHAR(255), 
      prepared_by_signature LONGTEXT, 
      approved_by_name VARCHAR(255) NULL, 
      approved_by_signature LONGTEXT NULL, 
      received_by_name VARCHAR(255) NULL, 
      received_by_signature LONGTEXT NULL, 
      total_amount DECIMAL(10,2), 
      items JSON, 
      preparer_email VARCHAR(255), 
      approver_email VARCHAR(255), 
      receiver_email VARCHAR(255), 
      status ENUM('prepared','approved','received') DEFAULT 'prepared', 
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
*/