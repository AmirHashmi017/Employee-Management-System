<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';
$employee = null;

// Get employee ID from URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch employee data
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $employee = $result->fetch_assoc();
    } else {
        header("Location: employees.php");
        exit();
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $position = sanitize($_POST['position']);
    $salary = sanitize($_POST['salary']);
    $hire_date = sanitize($_POST['hire_date']);
    
    if (!empty($name) && !empty($email) && !empty($position)) {
        // Check if email exists for other employees
        $check_stmt = $conn->prepare("SELECT id FROM employees WHERE email = ? AND id != ?");
        $check_stmt->bind_param("si", $email, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email already exists for another employee!";
        } else {
            $stmt = $conn->prepare("UPDATE employees SET name = ?, email = ?, phone = ?, position = ?, salary = ?, hire_date = ? WHERE id = ?");
            $stmt->bind_param("ssssdsi", $name, $email, $phone, $position, $salary, $hire_date, $id);
            
            if ($stmt->execute()) {
                $success = "Employee updated successfully!";
                // Refresh employee data
                $employee['name'] = $name;
                $employee['email'] = $email;
                $employee['phone'] = $phone;
                $employee['position'] = $position;
                $employee['salary'] = $salary;
                $employee['hire_date'] = $hire_date;
            } else {
                $error = "Error updating employee!";
            }
            $stmt->close();
        }
        $check_stmt->close();
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
    <title>Edit Employee - Employee Management</title>
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
        input[type="email"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
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
        <h1>✏️ Edit Employee</h1>
        <a href="employees.php">← Back to Employees</a>
    </div>
    
    <div class="container">
        <div class="form-container">
            <h2>Update Employee Information</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($employee): ?>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="position">Position <span class="required">*</span></label>
                        <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($employee['position']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="salary">Salary ($)</label>
                        <input type="number" id="salary" name="salary" step="0.01" min="0" value="<?php echo $employee['salary']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" id="hire_date" name="hire_date" value="<?php echo $employee['hire_date']; ?>">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-submit">Update Employee</button>
                    <a href="employees.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>