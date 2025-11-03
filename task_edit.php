<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';
$task = null;

// Fetch all employees for dropdown
$employees = $conn->query("SELECT id, name FROM employees ORDER BY name ASC");

// Get task ID from URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch task data
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $task = $result->fetch_assoc();
    } else {
        header("Location: tasks.php");
        exit();
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $employee_id = intval($_POST['employee_id']);
    $task_title = sanitize($_POST['task_title']);
    $task_description = sanitize($_POST['task_description']);
    $priority = sanitize($_POST['priority']);
    $status = sanitize($_POST['status']);
    $deadline = sanitize($_POST['deadline']);
    
    if (!empty($employee_id) && !empty($task_title) && !empty($priority) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE tasks SET employee_id = ?, task_title = ?, task_description = ?, priority = ?, status = ?, deadline = ? WHERE id = ?");
        $stmt->bind_param("isssssi", $employee_id, $task_title, $task_description, $priority, $status, $deadline, $id);
        
        if ($stmt->execute()) {
            $success = "Task updated successfully!";
            // Refresh task data
            $task['employee_id'] = $employee_id;
            $task['task_title'] = $task_title;
            $task['task_description'] = $task_description;
            $task['priority'] = $priority;
            $task['status'] = $status;
            $task['deadline'] = $deadline;
        } else {
            $error = "Error updating task!";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all required fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Employee Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar h1 { font-size: 24px; }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 30px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        label .required {
            color: #f44336;
        }
        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            flex: 1;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
        }
        .btn-cancel {
            background: #e0e0e0;
            color: #333;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-cancel:hover {
            background: #d0d0d0;
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #efe;
            color: #2a2;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>✏️ Edit Task</h1>
        <a href="tasks.php">← Back to Tasks</a>
    </div>
    
    <div class="container">
        <div class="form-container">
            <h2>Update Task Information</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($task): ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="task_title">Task Title <span class="required">*</span></label>
                        <input type="text" id="task_title" name="task_title" value="<?php echo htmlspecialchars($task['task_title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="employee_id">Assign To <span class="required">*</span></label>
                        <select id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php 
                            $employees->data_seek(0); // Reset pointer
                            while($emp = $employees->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $emp['id']; ?>" <?php echo ($emp['id'] == $task['employee_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($emp['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input type="date" id="deadline" name="deadline" value="<?php echo $task['deadline']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="priority">Priority <span class="required">*</span></label>
                        <select id="priority" name="priority" required>
                            <option value="Low" <?php echo ($task['priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                            <option value="Medium" <?php echo ($task['priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="High" <?php echo ($task['priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status <span class="required">*</span></label>
                        <select id="status" name="status" required>
                            <option value="Pending" <?php echo ($task['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Progress" <?php echo ($task['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Completed" <?php echo ($task['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="task_description">Task Description</label>
                        <textarea id="task_description" name="task_description"><?php echo htmlspecialchars($task['task_description']); ?></textarea>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-submit">Update Task</button>
                    <a href="tasks.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>