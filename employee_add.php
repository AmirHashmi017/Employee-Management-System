<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $position = sanitize($_POST['position']);
    $salary = sanitize($_POST['salary']);
    $hire_date = sanitize($_POST['hire_date']);
    
    if (!empty($name) && !empty($email) && !empty($position)) {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM employees WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO employees (name, email, phone, position, salary, hire_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssds", $name, $email, $phone, $position, $salary, $hire_date);
            
            if ($stmt->execute()) {
                $success = "Employee added successfully!";
                header("Location: employees.php");
                exit();
            } else {
                $error = "Error adding employee!";
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
    <title>Add Employee - Employee Management</title>
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
        <h1>➕ Add New Employee</h1>
        <a href="employees.php">← Back to Employees</a>
    </div>
    
    <div class="container">
        <div class="form-container">
            <h2>Employee Information</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="position">Position <span class="required">*</span></label>
                        <input type="text" id="position" name="position" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="salary">Salary ($)</label>
                        <input type="number" id="salary" name="salary" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="hire_date">Hire Date</label>
                        <input type="date" id="hire_date" name="hire_date">
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-submit">Add Employee</button>
                    <a href="employees.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>