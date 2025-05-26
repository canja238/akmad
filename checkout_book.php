<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = (int)$_POST['book_id'];
    $student_id = (int)$_POST['student_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if book exists and is available
        $stmt = $conn->prepare("SELECT is_checked_out FROM books WHERE book_id = ? FOR UPDATE");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Book not found.");
        }
        
        $book = $result->fetch_assoc();
        if ($book['is_checked_out']) {
            throw new Exception("This book is already checked out.");
        }
        
        // Check out the book
        $stmt = $conn->prepare("INSERT INTO checkouts (student_id, book_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $book_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE books SET is_checked_out = 1 WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Book checked out successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header("Location: student_dashboard.php");
    exit;
}

header("Location: index.php");
exit;
?>