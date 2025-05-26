<?php
require_once __DIR__ . '/../db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Library System'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Library Management System</h1>
        <nav>
    <?php if (isset($_SESSION['admin'])): ?>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_books.php">Manage Books</a>
        <a href="logout.php">Logout</a>
    <?php elseif (isset($_SESSION['student_id'])): ?>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="student_login.php">Student Login</a>
        <a href="admin_login.php">Admin Login</a>
    <?php endif; ?>
</nav>
    </div>
</header>
<main class="container">