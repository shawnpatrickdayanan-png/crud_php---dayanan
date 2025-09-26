<?php
require_once 'db_connect.php';

// Handle search and pagination
$search = $_GET['search'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$recordsPerPage = 10;
$offset = ($page - 1) * $recordsPerPage;

// Build query with search functionality
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE student_id LIKE :search OR first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR course LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

// Get total records count
$countSql = "SELECT COUNT(*) FROM students " . $whereClause;
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Get students data
$sql = "SELECT * FROM students " . $whereClause . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll();

// Handle delete action
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    
    try {
        $deleteSql = "DELETE FROM students WHERE id = :id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute([':id' => $deleteId]);
        
        header("Location: select.php?message=Student deleted successfully&type=success");
        exit();
    } catch(PDOException $e) {
        $message = "Error deleting student: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Handle messages
$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .badge-year {
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i>Student Lab CRUD
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="insert.php">
                    <i class="bi bi-person-plus me-1"></i>Add Student
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-people-fill me-2"></i>Students List
                    <span class="badge bg-light text-primary ms-2"><?php echo $totalRecords; ?> Total</span>
                </h4>
                <a href="insert.php" class="btn btn-light btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Add New Student
                </a>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Search Form -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <input type="text" class="form-control me-2" name="search" placeholder="Search students..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                    <?php if (!empty($search)): ?>
                        <div class="col-md-6 text-end">
                            <a href="select.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear Search
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($students)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">
                            <?php echo empty($search) ? 'No students found' : 'No students match your search'; ?>
                        </h5>
                        <?php if (empty($search)): ?>
                            <a href="insert.php" class="btn btn-primary mt-3">
                                <i class="bi bi-person-plus me-1"></i>Add First Student
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Course</th>
                                    <th>Year</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?php echo htmlspecialchars($student['student_id']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($student['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($student['phone']): ?>
                                                <a href="tel:<?php echo htmlspecialchars($student['phone']); ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($student['phone']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($student['course']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info badge-year">
                                                <?php echo htmlspecialchars($student['year_level']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="update.php?id=<?php echo $student['id']; ?>" 
                                                   class="btn btn-outline-success btn-action" 
                                                   title="Edit Student">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-action" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-student-id="<?php echo $student['id']; ?>"
                                                        data-student-name="<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>"
                                                        title="Delete Student">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Students pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                
                                <?php for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        
                        <div class="text-center text-muted">
                            Showing <?php echo (($page-1) * $recordsPerPage + 1); ?> to 
                            <?php echo min($page * $recordsPerPage, $totalRecords); ?> of 
                            <?php echo $totalRecords; ?> students
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="studentName"></strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Student
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle delete modal
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var studentId = button.getAttribute('data-student-id');
            var studentName = button.getAttribute('data-student-name');
            
            var modalStudentName = deleteModal.querySelector('#studentName');
            var confirmDeleteLink = deleteModal.querySelector('#confirmDelete');
            
            modalStudentName.textContent = studentName;
            confirmDeleteLink.href = 'select.php?delete=' + studentId;
        });
    </script>
</body>
</html>