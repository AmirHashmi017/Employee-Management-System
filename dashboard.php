<?php
require_once 'config.php';
requireLogin();

$total_employees = $conn->query("SELECT COUNT(*) as count FROM employees")->fetch_assoc()['count'];
$total_tasks = $conn->query("SELECT COUNT(*) as count FROM tasks")->fetch_assoc()['count'];
$pending_tasks = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'Pending'")->fetch_assoc()['count'];
$completed_tasks = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'Completed'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Employee Management</title>
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
        .navbar h1 {
            font-size: 24px;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            transition: background 0.3s;
        }
        .navbar a:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            font-size: 40px;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        .stat-icon.employees { background: #e3f2fd; }
        .stat-icon.tasks { background: #f3e5f5; }
        .stat-icon.pending { background: #fff3e0; }
        .stat-icon.completed { background: #e8f5e9; }
        .stat-info h3 {
            color: #666;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .stat-info p {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .menu-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .menu-card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 22px;
        }
        .menu-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1> Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                
                <div class="stat-info">
                    <h3>Total Employees</h3>
                    <p><?php echo $total_employees; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                
                <div class="stat-info">
                    <h3>Total Tasks</h3>
                    <p><?php echo $total_tasks; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                
                <div class="stat-info">
                    <h3>Pending Tasks</h3>
                    <p><?php echo $pending_tasks; ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                
                <div class="stat-info">
                    <h3>Completed Tasks</h3>
                    <p><?php echo $completed_tasks; ?></p>
                </div>
            </div>
        </div>
        
        <div class="menu-grid">
            <div class="menu-card">
                <h2> Employee Management</h2>
                <p>View, add, edit, and delete employee records. Manage employee information including contact details, position, and salary.</p>
                <a href="employees.php" class="btn">Manage Employees</a>
            </div>
            
            <div class="menu-card">
                <h2> Task Management</h2>
                <p>Assign and track tasks for employees. Monitor task status, priorities, and deadlines to ensure project success.</p>
                <a href="tasks.php" class="btn">Manage Tasks</a>
            </div>
        </div>
    </div>
</body>
</html>