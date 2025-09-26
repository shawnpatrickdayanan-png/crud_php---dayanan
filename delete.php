<?php
require_once 'db_connect.php';

// Get student ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: select.php');
    exit;
}

$student_id = (int)$_GET['id'];

try {
    // First, check if student exists
    $sql = "SELECT name FROM students WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $_SESSION['message'] = "Student not found!";
        $_SESSION['messageType'] = "warning";
        header('Location: select.php');
        exit;
    }

    // Delete the student
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);

    // Start session to store success message
    session_start();
    $_SESSION['message'] = "Student '{$student['name']}' deleted successfully!";
    $_SESSION['messageType'] = "success";

} catch(PDOException $e) {
    session_start();
    $_SESSION['message'] = "Error deleting student: " . $e->getMessage();
    $_SESSION['messageType'] = "danger";
}

// Redirect back to student list
header('Location: select.php');
exit;
?>