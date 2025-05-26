<?php
require_once 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];
$student_email = $_SESSION['student_email'];
$student_image = $_SESSION['student_image'] ?? null;

// Get student statistics
$total_checkouts = $conn->query("SELECT COUNT(*) FROM checkouts WHERE student_id = $student_id")->fetch_row()[0];
$active_checkouts = $conn->query("SELECT COUNT(*) FROM checkouts WHERE student_id = $student_id AND return_date IS NULL")->fetch_row()[0];
$overdue_checkouts = $conn->query("
    SELECT COUNT(*) 
    FROM checkouts 
    WHERE student_id = $student_id 
    AND return_date IS NULL 
    AND checkout_date < DATE_SUB(NOW(), INTERVAL 14 DAY)
")->fetch_row()[0];

// Get checked out books
$checked_out_books = $conn->query("
    SELECT c.checkout_id, b.book_id, b.title, b.author, c.checkout_date,
           DATEDIFF(NOW(), c.checkout_date) AS days_out
    FROM checkouts c
    JOIN books b ON c.book_id = b.book_id
    WHERE c.student_id = $student_id AND c.return_date IS NULL
    ORDER BY c.checkout_date DESC
");

$pageTitle = "Student Dashboard";
include 'includes/header.php';
?>

<div class="container">
    <div class="profile-header">
        <h1>Welcome, <?php echo htmlspecialchars($student_name); ?></h1>
        <div class="profile-avatar">
            <?php if ($student_image): ?>
                <img src="<?php echo htmlspecialchars($student_image); ?>" alt="Profile Image">
            <?php else: ?>
                <div class="avatar-initials"><?php echo strtoupper(substr($student_name, 0, 1)); ?></div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Your Activity</h3>
            <div class="stat-number"><?php echo $total_checkouts; ?></div>
            <div class="stat-label">Total Checkouts</div>
            <div class="stat-number"><?php echo $active_checkouts; ?></div>
            <div class="stat-label">Books Out Now</div>
            <?php if ($overdue_checkouts > 0): ?>
                <div class="stat-number text-danger"><?php echo $overdue_checkouts; ?></div>
                <div class="stat-label">Overdue Books</div>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-card">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="search_books.php?student_id=<?php echo $student_id; ?>" class="btn btn-primary">Find Books</a>
                <a href="#" class="btn btn-secondary">View History</a>
            </div>
        </div>
    </div>
    
    <div class="card mt-4">
        <h2>Your Checked Out Books</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        
        <?php if ($checked_out_books->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Checkout Date</th>
                        <th>Days Out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $checked_out_books->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['checkout_date'])); ?></td>
                            <td><?php echo $row['days_out']; ?></td>
                            <td>
                                <?php if ($row['days_out'] > 14): ?>
                                    <span class="badge badge-danger">Overdue</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Due in <?php echo 14 - $row['days_out']; ?> days</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" action="return_book.php">
                                    <input type="hidden" name="checkout_id" value="<?php echo $row['checkout_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Return</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                You don't have any books checked out currently.
                <a href="search_books.php?student_id=<?php echo $student_id; ?>" class="alert-link">Find books to check out</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>