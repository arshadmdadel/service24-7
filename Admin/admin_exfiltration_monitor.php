<?php
// filepath: c:\xampp\htdocs\service24_today\Admin\admin_exfiltration_monitor.php

class AdminExfiltrationMonitor {
    private $conn;
    
    // Thresholds for MAJOR admin (MORE PERMISSIVE - can export large data)
    private $major_threshold_file_size = 52428800; // 50MB
    private $major_threshold_requests = 50; 
    private $major_threshold_data_transfer = 104857600; // 100MB per minute
    
    // Thresholds for PRIMARY admin (STRICTER - limited exports)
    private $primary_threshold_file_size = 51200; 
    private $primary_threshold_requests = 5; // requests per minute
    private $primary_threshold_data_transfer = 1048576; 
    
    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'service24/7');
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    // Monitor admin data export attempts
    public function monitorAdminExport($admin_id, $admin_role, $action_type, $data_size, $file_count = 1) {
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // SWAPPED: Major admin gets higher limits, Primary gets lower limits
        $threshold_file_size = ($admin_role === 'major') ? 
            $this->major_threshold_file_size : $this->primary_threshold_file_size;
        $threshold_requests = ($admin_role === 'major') ? 
            $this->major_threshold_requests : $this->primary_threshold_requests;
        $threshold_data_transfer = ($admin_role === 'major') ? 
            $this->major_threshold_data_transfer : $this->primary_threshold_data_transfer;
        
        // Check recent activity
        $stmt = $this->conn->prepare("
            SELECT SUM(data_size) as total_size, COUNT(*) as request_count 
            FROM exfiltration_logs 
            WHERE admin_id = ? 
            AND timestamp > (NOW() - INTERVAL 1 MINUTE)
        ");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        $is_suspicious = false;
        $should_block = false;
        
        // Detect suspicious patterns
        if ($data_size > $threshold_file_size) {
            $is_suspicious = true;
            // PRIMARY admin gets blocked immediately for large files
            if ($admin_role === 'primary') {
                $should_block = true;
            }
        }
        
        if ($result['request_count'] >= $threshold_requests) {
            $is_suspicious = true;
            $should_block = true;
        }
        
        if (($result['total_size'] + $data_size) > $threshold_data_transfer) {
            $is_suspicious = true;
            $should_block = true;
        }
        
        // Log the activity
        $log_stmt = $this->conn->prepare("
            INSERT INTO exfiltration_logs 
            (admin_id, ip_address, action_type, data_size, file_count, is_suspicious, blocked) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $log_stmt->bind_param("issiiib", $admin_id, $ip, $action_type, $data_size, $file_count, $is_suspicious, $should_block);
        $log_stmt->execute();
        $log_stmt->close();
        
        // Send alert if suspicious
        if ($is_suspicious) {
            $this->sendAdminAlert($admin_id, $admin_role, $ip, $action_type, $data_size, $should_block);
        }
        
        // Format size for display
        $size_display = $this->formatBytes($threshold_file_size);
        $data_size_display = $this->formatBytes($data_size);
        
        return [
            'allowed' => !$should_block,
            'suspicious' => $is_suspicious,
            'message' => $should_block ? 
                " Suspicious data transfer detected. Action blocked for security.\n\nYour role: $admin_role\nMax file size: $size_display\nYour file size: $data_size_display\n\nYour role has limited export permissions." : 'OK',
            'threshold_info' => [
                'role' => $admin_role,
                'max_file_size' => $threshold_file_size,
                'max_requests' => $threshold_requests,
                'max_data_transfer' => $threshold_data_transfer
            ]
        ];
    }
    
    // Format bytes to human readable
    private function formatBytes($bytes) {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    // Send alert
    private function sendAdminAlert($admin_id, $admin_role, $ip, $action_type, $data_size, $blocked) {
        // Get admin details
        $stmt = $this->conn->prepare("SELECT username, email FROM admin WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        // Log to file
        $alert_message = date('Y-m-d H:i:s') . " - ADMIN ALERT\n";
        $alert_message .= "Admin: {$admin['username']} (Role: $admin_role)\n";
        $alert_message .= "IP: $ip\n";
        $alert_message .= "Action: $action_type\n";
        $alert_message .= "Size: " . $this->formatBytes($data_size) . "\n";
        $alert_message .= "Status: " . ($blocked ? "BLOCKED" : "ALLOWED") . "\n";
        $alert_message .= "-------------------------------------------\n\n";
        
        file_put_contents(__DIR__ . '/admin_security_alerts.txt', $alert_message, FILE_APPEND);
        
        // Notify MAJOR admin if PRIMARY admin is blocked
        if ($admin_role === 'primary') {
            $notify_stmt = $this->conn->prepare("
                INSERT INTO admin_activity_log (admin_id, action, details, ip_address) 
                VALUES (?, 'security_alert', ?, ?)
            ");
            $details = "Primary admin {$admin['username']} triggered security alert: $action_type (" . 
                      $this->formatBytes($data_size) . ") - BLOCKED due to export limit";
            $notify_stmt->bind_param("iss", $admin_id, $details, $ip);
            $notify_stmt->execute();
            $notify_stmt->close();
        }
        
        // Update alert sent flag
        $update_stmt = $this->conn->prepare("
            UPDATE exfiltration_logs 
            SET alert_sent = 1 
            WHERE admin_id = ? 
            AND timestamp > (NOW() - INTERVAL 1 MINUTE)
        ");
        $update_stmt->bind_param("i", $admin_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
    
    // Check if admin is currently blocked
    public function isAdminBlocked($admin_id) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as block_count 
            FROM exfiltration_logs 
            WHERE admin_id = ? 
            AND blocked = 1 
            AND timestamp > (NOW() - INTERVAL 5 MINUTE)
        ");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return $result['block_count'] > 0;
    }
    
    // Log admin activity
    public function logActivity($admin_id, $action, $details = null) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->conn->prepare("
            INSERT INTO admin_activity_log (admin_id, action, details, ip_address) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $admin_id, $action, $details, $ip);
        $stmt->execute();
        $stmt->close();
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>