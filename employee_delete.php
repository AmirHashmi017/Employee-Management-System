<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Employee deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting employee!";
    }
    $stmt->close();
}

header("Location: employees.php");
exit();
?>