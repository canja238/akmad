<?php
require_once 'db.php';

if (isset($_SESSION['student_id'])) {
    header("Location: student_dashboard.php");
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $image_path = null;
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/students/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Validate image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        if (!in_array($file_type, $allowed_types)) {
            $message = "Only JPG, PNG, and GIF files are allowed.";
            $message_type = "danger";
        } else {
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            } else {
                $message = "Error uploading image.";
                $message_type = "danger";
            }
        }
    }
    
    if (!empty($name) && !empty($email) && empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) {
                $stmt = $conn->prepare("INSERT INTO students (name, email, image_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $image_path);
                
                if ($stmt->execute()) {
                    $student_id = $stmt->insert_id;
                    $_SESSION['student_id'] = $student_id;
                    $_SESSION['student_name'] = $name;
                    $_SESSION['student_email'] = $email;
                    $_SESSION['student_image'] = $image_path;
                    
                    header("Location: student_dashboard.php");
                    exit;
                } else {
                    $message = "Error during registration. Please try again.";
                    $message_type = "danger";
                }
            } else {
                $message = "Email already exists!";
                $message_type = "danger";
            }
        } else {
            $message = "Invalid email format!";
            $message_type = "danger";
        }
    } elseif (empty($message)) {
        $message = "All fields are required!";
        $message_type = "danger";
    }
}

$pageTitle = "Student Registration";
include 'includes/header.php';
?>

<div class="card">
    <h1>Register as Student</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="image">Profile Image (optional):</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/jpeg, image/png, image/gif">
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    
    <div class="mt-3">
        <p>Already registered? <a href="student_login.php">Login here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>