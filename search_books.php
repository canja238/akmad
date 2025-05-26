<?php
require_once 'db.php';

if (!isset($_GET['student_id'])) {
    header("Location: register.php");
    exit;
}

$student_id = (int)$_GET['student_id'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Verify student exists
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Invalid student ID");
}

// Build search query
$query = "SELECT book_id, title, author FROM books WHERE is_checked_out = 0";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR author LIKE ?)";
    $search_term = "%$search%";
    $params = [$search_term, $search_term];
    $types = 'ss';
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books = $stmt->get_result();

$pageTitle = "Search Books";
include 'includes/header.php';
?>


<div class="card">
    <h1>Search Available Books</h1>
    
    <div class="search-container">
        <form method="get" class="flex-grow-1">
            <input type="text" name="search" class="form-control" placeholder="Search by title, author, or ISBN" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
        </form>
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    
    <?php if ($books->num_rows > 0): ?>
        <div class="mt-4">
            <div class="alert alert-success">
                Found <?php echo $books->num_rows; ?> available books matching your search.
            </div>
            
            <div class="book-grid">
                <?php while ($row = $books->fetch_assoc()): ?>
                    <div class="book-card">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <div class="book-author">by <?php echo htmlspecialchars($row['author']); ?></div>
                        <div class="book-meta">
                            <span class="badge badge-success">Available</span>
                        </div>
                        <form method="post" action="checkout_book.php" class="mt-3">
                            <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                            <button type="submit" class="btn btn-primary btn-block">Check Out</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">
            No available books found matching your search.
            <?php if (!empty($search)): ?>
                <a href="search_books.php?student_id=<?php echo $student_id; ?>" class="alert-link">Clear search</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>