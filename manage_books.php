<?php
require_once 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$message = '';
$message_type = '';

// Add new book
if (isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    
    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO books (title, author) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $author);
        
        if ($stmt->execute()) {
            $message = "Book added successfully!";
            $message_type = "success";
        } else {
            $message = "Error adding book: " . $conn->error;
            $message_type = "danger";
        }
    } else {
        $message = "Title is required!";
        $message_type = "danger";
    }
}
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if (!empty($search)) {
    $search_term = "%$search%";
    $where = " WHERE title LIKE ? OR author LIKE ?";
}

// Update the books query
$query = "SELECT * FROM books $where ORDER BY title";
$books = $conn->prepare($query);
if (!empty($search)) {
    $books->bind_param("ss", $search_term, $search_term);
}
$books->execute();
$books = $books->get_result();
// Delete book
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "Book deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting book: " . $conn->error;
        $message_type = "danger";
    }
}

// Update book
if (isset($_POST['edit'])) {
    $id = (int)$_POST['book_id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    
    if (!empty($title)) {
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ? WHERE book_id = ?");
        $stmt->bind_param("ssi", $title, $author, $id);
        
        if ($stmt->execute()) {
            $message = "Book updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating book: " . $conn->error;
            $message_type = "danger";
        }
    } else {
        $message = "Title is required!";
        $message_type = "danger";
    }
}

// Get all books
$books = $conn->query("SELECT * FROM books ORDER BY title");

$pageTitle = "Manage Books";
include 'includes/header.php';
?>


<div class="card">
    <h1>Manage Books</h1>
    
    <div class="search-container">
        <form method="get" class="flex-grow-1">
            <input type="text" name="search" class="form-control" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <h2>Add New Book</h2>
        <form method="post">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" id="author" name="author" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" id="isbn" name="isbn" class="form-control">
            </div>
            
            <button type="submit" name="add" class="btn btn-primary">Add Book</button>
        </form>
    </div>
    
    <div class="card mt-4">
        <h2>All Books (<?php echo $books->num_rows; ?>)</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $books->fetch_assoc()): ?>
                    <tr>
                        <form method="post">
                            <td><?php echo $row['book_id']; ?></td>
                            <td>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($row['title']); ?>">
                                <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                            </td>
                            <td>
                                <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($row['author']); ?>">
                            </td>
                            <td>
                                <?php if ($row['is_checked_out']): ?>
                                    <span class="badge badge-danger">Checked Out</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button name="edit" class="btn btn-success btn-sm">Update</button>
                                <a href="?delete=<?php echo $row['book_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>