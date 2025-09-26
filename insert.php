<?php
require_once 'db_connect.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_POST) {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course = trim($_POST['course']);
    $year_level = $_POST['year_level'];
    
    // Server-side validation
    $errors = [];
    
    if (empty($student_id)) $errors[] = "Student ID is required";
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($course)) $errors[] = "Course is required";
    if (empty($year_level)) $errors[] = "Year level is required";
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO students (student_id, first_name, last_name, email, phone, course, year_level) 
                    VALUES (:student_id, :first_name, :last_name, :email, :phone, :course, :year_level)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':student_id' => $student_id,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':phone' => $phone,
                ':course' => $course,
                ':year_level' => $year_level
            ]);
            
            $message = "Student added successfully!";
            $messageType = "success";
            
            // Clear form data
            $_POST = [];
            
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Error: Student ID or Email already exists!";
            } else {
                $message = "Error: " . $e->getMessage();
            }
            $messageType = "danger";
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
    <title>Add New Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i>Student Lab CRUD
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="select.php">
                    <i class="bi bi-list-ul me-1"></i>View Students
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-person-plus me-2"></i>Add New Student
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" 
                                           value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please provide a valid student ID.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="year_level" class="form-label">Year Level <span class="text-danger">*</span></label>
                                    <select class="form-select" id="year_level" name="year_level" required>
                                        <option value="">Choose...</option>
                                        <option value="1st Year" <?php echo ($_POST['year_level'] ?? '') == '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                                        <option value="2nd Year" <?php echo ($_POST['year_level'] ?? '') == '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                                        <option value="3rd Year" <?php echo ($_POST['year_level'] ?? '') == '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                                        <option value="4th Year" <?php echo ($_POST['year_level'] ?? '') == '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a year level.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please provide a first name.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                                    <div class="invalid-feedback">Please provide a last name.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                <div class="invalid-feedback">Please provide a valid email address.</div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                       placeholder="e.g., +63 912 345 6789">
                            </div>

                            <div class="mb-3">
                                <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="course" name="course" 
                                       value="<?php echo htmlspecialchars($_POST['course'] ?? ''); ?>"
                                       placeholder="e.g., Bachelor of Science in Computer Science" required>
                                <div class="invalid-feedback">Please provide a course.</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="select.php" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-arrow-left me-1"></i>Back to List
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Add Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>