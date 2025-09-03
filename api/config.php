<?php
// Database Configuration untuk Hutan Harmoni Leaderboard
// Host: multinteraktif.online/classpoint/html-package/HutanHarmoni/api/

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
class Database {
    private $host = 'localhost';
    private $db_name = 'n1567943_mediainteraktif_db';
    private $username = 'n1567943_mediainteraktif_user';
    private $password = 'Gitar222@@@';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}

// Utility functions
function sendResponse($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function sendError($message, $status_code = 400) {
    sendResponse(['error' => $message, 'success' => false], $status_code);
}

function validateInput($data, $required_fields) {
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            return false;
        }
        
        // Special handling for numeric fields (allow 0)
        if (in_array($field, ['total_mistakes', 'score'])) {
            if (!is_numeric($data[$field]) && $data[$field] !== 0 && $data[$field] !== '0') {
                return false;
            }
        } else {
            // String fields - check if empty after trim
            if (empty(trim($data[$field]))) {
                return false;
            }
        }
    }
    return true;
}

// Generate unique session ID
function generateSessionId() {
    return uniqid('hh_', true);
}
?>