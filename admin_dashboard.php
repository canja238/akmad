<?php
require_once 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Get statistics
$total_books = $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0];
$available_books = $conn->query("SELECT COUNT(*) FROM books WHERE is_checked_out = 0")->fetch_row()[0];
$checked_out_books = $total_books - $available_books;
$total_students = $conn->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$active_checkouts = $conn->query("SELECT COUNT(*) FROM checkouts WHERE return_date IS NULL")->fetch_row()[0];

// Get recent checkouts
$recent_checkouts = $conn->query("
    SELECT b.title, s.name, s.email, c.checkout_date 
    FROM checkouts c
    JOIN books b ON c.book_id = b.book_id
    JOIN students s ON c.student_id = s.student_id
    WHERE c.return_date IS NULL
    ORDER BY c.checkout_date DESC
    LIMIT 5
");

$pageTitle = "Admin Dashboard";
include 'includes/header.php';
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Books</h3>
            <div class="stat-number"><?php echo $total_books; ?></div>
            <div class="stat-label">Total Books</div>
            <div class="action-buttons">
                <a href="manage_books.php" class="btn btn-primary">Manage Books</a>
            </div>
        </div>
        
        <div class="dashboard-card">
            <h3>Availability</h3>
            <div class="stat-number"><?php echo $available_books; ?></div>
            <div class="stat-label">Available Books</div>
            <div class="stat-number"><?php echo $checked_out_books; ?></div>
            <div class="stat-label">Checked Out</div>
        </div>
        
        <div class="dashboard-card">
            <h3>Students</h3>
            <div class="stat-number"><?php echo $total_students; ?></div>
            <div class="stat-label">Registered Students</div>
            <div class="stat-number"><?php echo $active_checkouts; ?></div>
            <div class="stat-label">Active Checkouts</div>
        </div>
    </div>
    
    <div class="card mt-4">
        <h2>Recent Checkouts</h2>
        
        <?php if ($recent_checkouts->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Student</th>
                        <th>Checkout Date</th>
                        <th>Days Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $recent_checkouts->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar"><?php echo strtoupper(substr($row['name'], 0, 1)); ?></div>
                                    <div>
                                        <?php echo htmlspecialchars($row['name']); ?>
                                        <div class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($row['checkout_date'])); ?></td>
                            <td><?php echo floor((time() - strtotime($row['checkout_date'])) / (60 * 60 * 24)); ?> days</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No recent checkouts found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>