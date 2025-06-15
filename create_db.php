<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `authentication_assignment` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "Database 'authentication_assignment' created or already exists!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
