<?php
require_once 'db_connect.php';

$message = '';
$messageType = '';
$student = null;

// Get student ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: select.php');
    exit;
}

$student_id = (int)$_GET['id'];

// Fetch student data
try {
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        header('Location: select.php');
        exit;
    }
} catch(PDOException $e) {
    $message = "Error: " . $e->getMessage();
    $messageType = "danger";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course = trim($_POST['course']);

    // Form validation
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($phone)) {
        $errors[] = "Phone is required";
    }

    if (empty($course)) {
        $errors[] = "Course is required";
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE students SET name = ?, email = ?, phone = ?, course = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $phone, $course, $student_id]);

            $message = "Student updated successfully!";
            $messageType = "success";

            // Refresh student data
            $student['name'] = $name;
            $student['email'] = $email;
            $student['phone'] = $phone;
            $student['course'] = $course;

        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Email already exists!";
                $messageType = "danger";
            } else {
                $message = "Error: " . $e->getMessage();
                $messageType = "danger";
            }
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Update Student</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($student): ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="<?php echo htmlspecialchars($student['name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                                    <select class="form-select" id="course" name="course" required>
                                        <option value="">Select Course</option>
                                        <option value="Computer Science" <?php echo ($student['course'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                                        <option value="Information Technology" <?php echo ($student['course'] == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                                        <option value="Business Administration" <?php echo ($student['course'] == 'Business Administration') ? 'selected' : ''; ?>>Business Administration</option>
                                        <option value="Engineering" <?php echo ($student['course'] == 'Engineering') ? 'selected' : ''; ?>>Engineering</option>
                                        <option value="Mathematics" <?php echo ($student['course'] == 'Mathematics') ? 'selected' : ''; ?>>Mathematics</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted">
                                        <strong>Created:</strong> <?php echo date('Y-m-d H:i', strtotime($student['created_at'])); ?>
                                        <?php if ($student['updated_at'] != $student['created_at']): ?>
                                            <br><strong>Last Updated:</strong> <?php echo date('Y-m-d H:i', strtotime($student['updated_at'])); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="select.php" class="btn btn-secondary me-md-2">Back to List</a>
                                    <button type="submit" class="btn btn-primary">Update Student</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>