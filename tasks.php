<?php
require_once 'config.php';
requireLogin();

// Fetch all tasks with employee names
$sql = "SELECT t.*, e.name as employee_name 
        FROM tasks t 
        LEFT JOIN employees e ON t.employee_id = e.id 
        ORDER BY t.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - Employee Management</title>
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
        .navbar .nav-links {
            display: flex;
            gap: 15px;
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
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            font-weight: 600;
        }
        tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.2s;
        }
        tbody tr:hover {
            background: #f5f7fa;
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .priority-low {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .priority-medium {
            background: #fff3e0;
            color: #ef6c00;
        }
        .priority-high {
            background: #ffebee;
            color: #c62828;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3e0;
            color: #f57c00;
        }
        .status-progress {
            background: #e3f2fd;
            color: #1976d2;
        }
        .status-completed {
            background: #e8f5e9;
            color: #388e3c;
        }
        .action-btns {
            display: flex;
            gap: 10px;
        }
        .btn-edit {
            background: #4caf50;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-delete {
            background: #f44336;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .btn-edit:hover {
            background: #45a049;
        }
        .btn-delete:hover {
            background: #da190b;
        }
        .no-data {
            padding: 40px;
            text-align: center;
            color: #666;
        }
        .task-description {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
            animation: slideIn 0.3s;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .modal-header {
            background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 10px 10px 0 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .modal-header h2 {
            font-size: 22px;
            margin: 0;
        }
        .modal-body {
            padding: 30px;
        }
        .modal-body p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .modal-body .warning-text {
            color: #f44336;
            font-weight: 600;
            margin-top: 15px;
        }
        .modal-footer {
            padding: 20px 30px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #e0e0e0;
        }
        .modal-btn {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .modal-btn-cancel {
            background: #e0e0e0;
            color: #333;
        }
        .modal-btn-cancel:hover {
            background: #d0d0d0;
        }
        .modal-btn-confirm {
            background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);
            color: white;
        }
        .modal-btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.4);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1> Task Management</h1>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="employees.php">Employees</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="header-section">
            <h2>All Tasks</h2>
            <a href="task_add.php" class="btn-add">+ Add New Task</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Task Title</th>
                        <th>Assigned To</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['task_title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                                <td>
                                    <div class="task-description" title="<?php echo htmlspecialchars($row['task_description']); ?>">
                                        <?php echo htmlspecialchars($row['task_description']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="priority-badge priority-<?php echo strtolower($row['priority']); ?>">
                                        <?php echo $row['priority']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $statusClass = 'status-pending';
                                    if ($row['status'] == 'In Progress') $statusClass = 'status-progress';
                                    if ($row['status'] == 'Completed') $statusClass = 'status-completed';
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['deadline'] ? date('M d, Y', strtotime($row['deadline'])) : 'N/A'; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="task_edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                        <button type="button" class="btn-delete" onclick="openDeleteModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['task_title'])); ?>')">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">No tasks found. Add your first task!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Deletion</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this task?</p>
                <p><strong id="taskName"></strong></p>
                <p class="warning-text">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteForm" method="POST" action="task_delete.php" style="display: inline;">
                    <input type="hidden" name="id" id="deleteTaskId">
                    <button type="submit" class="modal-btn modal-btn-confirm">Delete Task</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(taskId, taskName) {
            document.getElementById('deleteTaskId').value = taskId;
            document.getElementById('taskName').textContent = taskName;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>