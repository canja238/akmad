<?php
require_once 'db.php';

if (isset($_SESSION['student_id'])) {
    header("Location: student_dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        $_SESSION['student_id'] = $student['student_id'];
        $_SESSION['student_name'] = $student['name'];
        $_SESSION['student_email'] = $student['email'];
        $_SESSION['student_image'] = $student['image_path'];
        
        header("Location: student_dashboard.php");
        exit;
    } else {
        $error = "Invalid email address";
    }
}

$pageTitle = "Student Login";
include 'includes/header.php';
?>

<div class="card">
    <h1>Student Login</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    
    <div class="mt-3">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
        <p><a href="index.php">Back to home</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>