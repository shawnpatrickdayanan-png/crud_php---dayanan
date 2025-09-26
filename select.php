<?php
require_once 'db_connect.php';
session_start();

// Handle sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column
$allowedSorts = ['name', 'email', 'phone', 'course', 'created_at'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'name';
}

// Validate order
$order = strtoupper($order);
if ($order !== 'ASC' && $order !== 'DESC') {
    $order = 'ASC';
}

try {
    $sql = "SELECT * FROM students ORDER BY $sort $order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
}

// Function to create sort URL
function getSortUrl($column, $currentSort, $currentOrder) {
    if ($column === $currentSort) {
        $newOrder = ($currentOrder === 'ASC') ? 'DESC' : 'ASC';
    } else {
        $newOrder = 'ASC';
    }
    return "?sort=$column&order=$newOrder";
}

// Function to get sort icon
function getSortIcon($column, $currentSort, $currentOrder) {
    if ($column === $currentSort) {
        return $currentOrder === 'ASC' ? '▲' : '▼';
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Students List</h3>
                <a href="insert.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Student
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['messageType']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    unset($_SESSION['message']);
                    unset($_SESSION['messageType']);
                    ?>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php elseif (empty($students)): ?>
                    <div class="alert alert-info text-center">
                        <h4>No students found</h4>
                        <p>Click "Add New Student" to get started.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>
                                        <a href="<?php echo getSortUrl('name', $sort, $order); ?>" class="text-white text-decoration-none">
                                            Name <?php echo getSortIcon('name', $sort, $order); ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?php echo getSortUrl('email', $sort, $order); ?>" class="text-white text-decoration-none">
                                            Email <?php echo getSortIcon('email', $sort, $order); ?>
                                        </a>
                                    </th>
                                    <th>Phone</th>
                                    <th>
                                        <a href="<?php echo getSortUrl('course', $sort, $order); ?>" class="text-white text-decoration-none">
                                            Course <?php echo getSortIcon('course', $sort, $order); ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?php echo getSortUrl('created_at', $sort, $order); ?>" class="text-white text-decoration-none">
                                            Created At <?php echo getSortIcon('created_at', $sort, $order); ?>
                                        </a>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($student['course']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($student['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="update.php?id=<?php echo $student['id']; ?>"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal<?php echo $student['id']; ?>">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>

                                            <!-- Delete Confirmation Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $student['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Delete</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete student <strong><?php echo htmlspecialchars($student['name']); ?></strong>?</p>
                                                            <p class="text-muted">This action cannot be undone.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-danger">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 text-muted">
                        Total students: <?php echo count($students); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>