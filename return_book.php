<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout_id'])) {
    $checkout_id = (int)$_POST['checkout_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get checkout record
        $stmt = $conn->prepare("SELECT book_id FROM checkouts WHERE checkout_id = ? AND return_date IS NULL");
        $stmt->bind_param("i", $checkout_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Invalid checkout record or book already returned.");
        }
        
        $checkout = $result->fetch_assoc();
        $book_id = $checkout['book_id'];
        
        // Update return date
        $stmt = $conn->prepare("UPDATE checkouts SET return_date = NOW() WHERE checkout_id = ?");
        $stmt->bind_param("i", $checkout_id);
        $stmt->execute();
        
        // Update book status
        $stmt = $conn->prepare("UPDATE books SET is_checked_out = 0 WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Book returned successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = $e->getMessage();
    }
}

header("Location: student_dashboard.php");
exit;
?>