<?php
require_once 'db_connect.php';

// This file serves as a dedicated delete endpoint
// The main delete functionality is integrated into select.php for better UX

$response = ['success' => false, 'message' => ''];

// Check if request method is POST for security
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get student ID from POST data
    $studentId = $_POST['id'] ?? null;
    
    if (!$studentId) {
        $response['message'] = 'Invalid student ID';
        echo json_encode($response);
        exit();
    }
    
    try {
        // First, check if student exists
        $checkSql = "SELECT id, first_name, last_name FROM students WHERE id = :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':id' => $studentId]);
        $student = $checkStmt->fetch();
        
        if (!$student) {
            $response['message'] = 'Student not found';
            echo json_encode($response);
            exit();
        }
        
        // Delete the student
        $deleteSql = "DELETE FROM students WHERE id = :id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute([':id' => $studentId]);
        
        if ($deleteStmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Student "' . $student['first_name'] . ' ' . $student['last_name'] . '" deleted successfully';
        } else {
            $response['message'] = 'Failed to delete student';
        }
        
    } catch(PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Handle GET request with redirect (for backward compatibility)
    $studentId = $_GET['id'] ?? null;
    
    if (!$studentId) {
        header("Location: select.php?message=" . urlencode("Invalid student ID") . "&type=danger");
        exit();
    }
    
    try {
        // First, get student info for confirmation message
        $checkSql = "SELECT first_name, last_name FROM students WHERE id = :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':id' => $studentId]);
        $student = $checkStmt->fetch();
        
        if (!$student) {
            header("Location: select.php?message=" . urlencode("Student not found") . "&type=danger");
            exit();
        }
        
        // Delete the student
        $deleteSql = "DELETE FROM students WHERE id = :id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute([':id' => $studentId]);
        
        if ($deleteStmt->rowCount() > 0) {
            $message = 'Student "' . $student['first_name'] . ' ' . $student['last_name'] . '" deleted successfully';
            header("Location: select.php?message=" . urlencode($message) . "&type=success");
        } else {
            header("Location: select.php?message=" . urlencode("Failed to delete student") . "&type=danger");
        }
        
    } catch(PDOException $e) {
        header("Location: select.php?message=" . urlencode("Database error occurred") . "&type=danger");
    }
    
    exit();
    
} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response for AJAX requests
header('Content-Type: application/json');
echo json_encode($response);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
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
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>Delete Student
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="bi bi-info-circle display-4 text-info"></i>
                        </div>
                        
                        <h5 class="mb-3">Delete Operation</h5>
                        
                        <p class="text-muted mb-4">
                            This page handles student deletion operations. For security and better user experience, 
                            deletion is primarily handled through the main students list page with proper confirmation dialogs.
                        </p>
                        
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>How to delete a student:</strong><br>
                            Go to the students list and click the <i class="bi bi-trash"></i> delete button next to any student record.
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="select.php" class="btn btn-primary">
                                <i class="bi bi-people-fill me-1"></i>Go to Students List
                            </a>
                            <a href="insert.php" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus me-1"></i>Add New Student
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- API Usage Information -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-code-slash me-2"></i>API Endpoint Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">This endpoint can be used for AJAX deletion:</p>
                        <div class="bg-light p-3 rounded">
                            <code class="small">
                                POST /delete.php<br>
                                Content-Type: application/x-www-form-urlencoded<br>
                                Body: id=STUDENT_ID
                            </code>
                        </div>
                        <p class="small text-muted mt-2 mb-0">
                            Returns JSON response with success status and message.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>