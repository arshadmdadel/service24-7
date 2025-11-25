<?php
// filepath: c:\xampp\htdocs\service24_today\Admin\index.php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include_once('admin_exfiltration_monitor.php');

$admin_id = $_SESSION['admin_id'];
$admin_role = $_SESSION['admin_role'];
$admin_username = $_SESSION['admin_username'];

$monitor = new AdminExfiltrationMonitor();

// ✅ Check if admin is blocked (checks admin table)
if ($monitor->isAdminBlocked($admin_id)) {
    $block_details = $monitor->getBlockDetails($admin_id);
    session_destroy();
    die("⚠️ Your account has been temporarily blocked due to suspicious activity.\n\n" .
        "Reason: " . htmlspecialchars($block_details['block_reason']) . "\n" .
        "Blocked until: " . htmlspecialchars($block_details['blocked_until']) . "\n\n" .
        "Please contact support or wait until the block expires.");
}

$conn = new mysqli('localhost', 'root', '', 'service24/7');

// Handle data export
if (isset($_GET['action']) && $_GET['action'] === 'export_users') {
    $result = $conn->query("SELECT * FROM user");
    $users = $result->fetch_all(MYSQLI_ASSOC);
    
    $data_size = strlen(json_encode($users));
    $file_count = count($users);
    
    // Monitor the export
    $check = $monitor->monitorAdminExport($admin_id, $admin_role, 'user_data_export', $data_size, $file_count);
    
    if (!$check['allowed']) {
        die($check['message']);
    }
    
    // Log activity
    $monitor->logActivity($admin_id, 'export_users', "Exported $file_count users");
    
    // Export CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users_export.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($users[0]));
    foreach ($users as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Handle AJAX requests for order stats
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['ajax']) {
        case 'total_orders':
            $result = $conn->query("SELECT COUNT(*) as count FROM `order`");
            echo json_encode($result->fetch_assoc());
            break;
            
        case 'pending_orders':
            $result = $conn->query("SELECT COUNT(*) as count FROM `order` WHERE accept_status = 0");
            echo json_encode($result->fetch_assoc());
            break;
            
        case 'completed_orders':
            $result = $conn->query("SELECT COUNT(*) as count FROM `order` WHERE accept_status = 2");
            echo json_encode($result->fetch_assoc());
            break;
            
        case 'total_workers':
            $result = $conn->query("SELECT COUNT(*) as count FROM worker");
            echo json_encode($result->fetch_assoc());
            break;
    }
    exit();
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM user")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM `order`")->fetch_assoc()['count'];
$total_workers = $conn->query("SELECT COUNT(*) as count FROM worker")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM `order` WHERE accept_status = 0")->fetch_assoc()['count'];
$completed_orders = $conn->query("SELECT COUNT(*) as count FROM `order` WHERE accept_status = 2")->fetch_assoc()['count'];

// ✅ Get recent suspicious activities WITH total file size in last 1 minute
$suspicious_activities = $conn->query("
    SELECT 
        e.*,
        a.username, 
        a.role,
        (SELECT COALESCE(SUM(e2.data_size), 0) 
         FROM exfiltration_logs e2 
         WHERE e2.admin_id = e.admin_id 
         AND e2.timestamp BETWEEN DATE_SUB(e.timestamp, INTERVAL 1 MINUTE) AND e.timestamp
        ) as total_size_in_minute
    FROM exfiltration_logs e 
    LEFT JOIN admin a ON e.admin_id = a.id 
    WHERE e.is_suspicious = 1 OR e.blocked = 1
    ORDER BY e.timestamp DESC 
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

// Get order types for pie chart
$order_types = $conn->query("
    SELECT service_name, COUNT(*) as count 
    FROM `order` 
    GROUP BY service_name
")->fetch_all(MYSQLI_ASSOC);

$labels = [];
$values = [];
foreach ($order_types as $row) {
    $labels[] = "Service " . $row['service_name'];
    $values[] = $row['count'];
}

// ONLY FOR MAJOR ADMIN - Get worker details
$workers = [];
if ($admin_role === 'major') {
    $workers = $conn->query("SELECT * FROM worker ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
}

// ONLY FOR MAJOR ADMIN - Get order details
$orders = [];
if ($admin_role === 'major') {
    $orders = $conn->query("
        SELECT o.*, u.username, u.email, w.fullname as worker_name ,o.time as order_date
        FROM `order` o 
        LEFT JOIN user u ON o.user_id = u.id 
        LEFT JOIN worker w ON o.worker_id = w.id 
        ORDER BY o.id DESC 
        LIMIT 50
    ")->fetch_all(MYSQLI_ASSOC);
}

// ✅ Helper function to format bytes
function formatBytes($bytes) {
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Service24/7</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 24px; }
        .role-badge {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-left: 10px;
        }
        .role-primary { background: #ffd700; color: #333; }
        .role-major { background: #ff6b6b; color: white; }
        
        .container { max-width: 1400px; margin: 20px auto; padding: 0 20px; }
        
        .stats {
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
            text-align: center;
            transition: transform 0.3s;
            cursor: pointer;
            position: relative;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .stat-card.locked {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .stat-card.locked::after {
            content: '🔒';
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
        }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; }
        .stat-card .number { font-size: 48px; font-weight: bold; color: #667eea; }
        .stat-card .icon { font-size: 40px; margin-bottom: 10px; }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            position: sticky;
            top: 0;
        }
        table tr:hover {
            background: #f8f9fa;
        }
        .alert { color: #ff6b6b; font-weight: bold; }
        .blocked { background: #ffe0e0; }
        
        /* ✅ Highlight total size column */
        .total-size-cell {
            background: #fff3cd;
            font-weight: bold;
            color: #856404;
        }
        
        .logout { 
            background: #ff6b6b; 
            color: white; 
            padding: 8px 20px; 
            border-radius: 5px; 
            text-decoration: none; 
        }
        .logout:hover { background: #ff5252; }
        
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 400px;
        }
        canvas {
            max-height: 350px;
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
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 1200px;
            max-height: 85vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-accepted { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .access-denied {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .access-denied .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>🛡️ Admin Dashboard</h1>
            <p>Welcome, <?= htmlspecialchars($admin_username) ?> 
                <span class="role-badge role-<?= $admin_role ?>">
                    <?= strtoupper($admin_role) ?> ADMIN
                </span>
            </p>
        </div>
        <div>
            <a href="admin_logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <!-- Stats Cards -->
        <div class="stats">
            <div class="stat-card <?= $admin_role !== 'major' ? 'locked' : '' ?>" onclick="<?= $admin_role === 'major' ? 'showOrdersModal()' : 'showAccessDenied()' ?>">
                <div class="icon">📦</div>
                <h3>Total Orders</h3>
                <div class="number"><?= $total_orders ?></div>
                <?php if ($admin_role !== 'major'): ?>
                    <small style="color: #999; display: block; margin-top: 10px;">Major Admin Only</small>
                <?php endif; ?>
            </div>
            <div class="stat-card">
                <div class="icon">⏳</div>
                <h3>Pending Orders</h3>
                <div class="number"><?= $pending_orders ?></div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <h3>Completed Orders</h3>
                <div class="number"><?= $completed_orders ?></div>
            </div>
            <div class="stat-card <?= $admin_role !== 'major' ? 'locked' : '' ?>" onclick="<?= $admin_role === 'major' ? 'showWorkersModal()' : 'showAccessDenied()' ?>">
                <div class="icon">👷</div>
                <h3>Total Workers</h3>
                <div class="number"><?= $total_workers ?></div>
                <?php if ($admin_role !== 'major'): ?>
                    <small style="color: #999; display: block; margin-top: 10px;">Major Admin Only</small>
                <?php endif; ?>
            </div>
            <div class="stat-card">
                <div class="icon">👥</div>
                <h3>Total Users</h3>
                <div class="number"><?= $total_users ?></div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-container">
            <div class="chart-box">
                <h3 style="text-align: center; margin-bottom: 15px;">Order Types Distribution</h3>
                <canvas id="orderTypeChart"></canvas>
            </div>
            <div class="chart-box">
                <h3 style="text-align: center; margin-bottom: 15px;">Order Status</h3>
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>

        <!-- Data Export Section -->
        <div class="section">
            <h2>📊 Data Export (Monitored)</h2>
            <p style="margin-bottom: 15px;">
                <?php if ($admin_role === 'major'): ?>
                    <strong>✅ Major Admin:</strong> Max 50MB/file, 50 requests/min, 100MB/min total
                    <span style="color: green; font-weight: bold;">(Full Export Access)</span>
                <?php else: ?>
                    <strong>⚠️ Primary Admin:</strong> Max 50KB/file, 5 requests/min, 1MB/min total
                    <span style="color: orange; font-weight: bold;">(Limited Export Access)</span>
                <?php endif; ?>
            </p>
            <a href="?action=export_users" class="btn btn-primary">Export All Users</a>
        </div>

        <!-- Suspicious Activities -->
        <div class="section">
            <h2>🚨 Recent Suspicious Activities</h2>
            <?php if (count($suspicious_activities) > 0): ?>
                <table>
                    <tr>
                        <th>Time</th>
                        <th>Admin</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>File Size</th>
                        <th style="background: #fff3cd;">📊 Total in 1min</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($suspicious_activities as $activity): ?>
                        <tr class="<?= $activity['blocked'] ? 'blocked' : '' ?>">
                            <td><?= htmlspecialchars($activity['timestamp']) ?></td>
                            <td><?= htmlspecialchars($activity['username'] ?? 'N/A') ?></td>
                            <td><span class="role-badge role-<?= strtolower($activity['role'] ?? 'unknown') ?>"><?= strtoupper($activity['role'] ?? 'N/A') ?></span></td>
                            <td><?= htmlspecialchars($activity['action_type']) ?></td>
                            <td><?= formatBytes($activity['data_size']) ?></td>
                            <td class="total-size-cell">
                                <?= formatBytes($activity['total_size_in_minute']) ?>
                            </td>
                            <td class="<?= $activity['blocked'] ? 'alert' : '' ?>">
                                <?= $activity['blocked'] ? '🚫 BLOCKED' : '⚠️ Flagged' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No suspicious activities detected.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Workers Modal (Major Admin Only) -->
    <div id="workersModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>👷 Workers List</h2>
                <span class="close" onclick="closeModal('workersModal')">&times;</span>
            </div>
            <?php if ($admin_role === 'major'): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Service Type</th>
                        <th>Status</th>
                        <th>Registration Date</th>
                    </tr>
                    <?php foreach ($workers as $worker): ?>
                        <tr>
                            <td><?= htmlspecialchars($worker['id']) ?></td>
                            <td><?= htmlspecialchars($worker['name']) ?></td>
                            <td><?= htmlspecialchars($worker['email']) ?></td>
                            <td><?= htmlspecialchars($worker['phone_number']) ?></td>
                            <td><?= htmlspecialchars($worker['address']) ?></td>
                            <td><?= htmlspecialchars($worker['service_type']) ?></td>
                            <td><?= $worker['approve_status'] ? '✅ Approved' : '⏳ Pending' ?></td>
                            <td><?= htmlspecialchars($worker['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="access-denied">
                    <div class="icon">🔒</div>
                    <h3>Access Denied</h3>
                    <p>Only Major Admins can view worker details</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Orders Modal (Major Admin Only) -->
    <div id="ordersModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>📦 Orders List</h2>
                <span class="close" onclick="closeModal('ordersModal')">&times;</span>
            </div>
            <?php if ($admin_role === 'major'): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Service</th>
                        <th>Worker</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td>
                                <?= htmlspecialchars($order['username']) ?><br>
                                <small><?= htmlspecialchars($order['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($order['service_name']) ?></td>
                            <td><?= htmlspecialchars($order['worker_name'] ?? 'Not Assigned') ?></td>
                            <td>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                switch($order['accept_status']) {
                                    case 0:
                                        $status_class = 'status-pending';
                                        $status_text = 'Pending';
                                        break;
                                    case 1:
                                        $status_class = 'status-accepted';
                                        $status_text = 'Accepted';
                                        break;
                                    case 2:
                                        $status_class = 'status-completed';
                                        $status_text = 'Completed';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                            </td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <div class="access-denied">
                    <div class="icon">🔒</div>
                    <h3>Access Denied</h3>
                    <p>Only Major Admins can view order details</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Order Types Pie Chart
        const ctx1 = document.getElementById('orderTypeChart').getContext('2d');
        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($values) ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Order Status Pie Chart
        const ctx2 = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Accepted', 'Completed'],
                datasets: [{
                    data: [
                        <?= $pending_orders ?>,
                        <?= $total_orders - $pending_orders - $completed_orders ?>,
                        <?= $completed_orders ?>
                    ],
                    backgroundColor: ['#ff6666', '#66b3ff', '#99ff99']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Modal Functions
        function showWorkersModal() {
            document.getElementById('workersModal').style.display = 'block';
        }

        function showOrdersModal() {
            document.getElementById('ordersModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function showAccessDenied() {
            alert('🔒 Access Denied\n\nOnly Major Admins can view this information.\n\nYour role: <?= strtoupper($admin_role) ?> ADMIN');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>