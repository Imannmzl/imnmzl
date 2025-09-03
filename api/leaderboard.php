<?php
require_once 'config.php';

class LeaderboardAPI {
    private $conn;
    private $table_name = "leaderboard_himpunan";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register new player
    public function registerPlayer($nama, $kelas) {
        try {
            // Generate session ID
            $session_id = generateSessionId();
            
            // Check if player already exists
            $check_query = "SELECT id, session_id FROM " . $this->table_name . " 
                           WHERE nama = :nama AND kelas = :kelas 
                           ORDER BY created_at DESC LIMIT 1";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(':nama', $nama);
            $check_stmt->bindParam(':kelas', $kelas);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
                return [
                    'success' => true,
                    'message' => 'Player sudah terdaftar',
                    'session_id' => $existing['session_id'],
                    'player_id' => $existing['id']
                ];
            }
            
            // Insert new player
            $insert_query = "INSERT INTO " . $this->table_name . " 
                            (nama, kelas, session_id, status, score, total_mistakes) 
                            VALUES (:nama, :kelas, :session_id, 'belum_selesai', 0, 0)";
            $insert_stmt = $this->conn->prepare($insert_query);
            $insert_stmt->bindParam(':nama', $nama);
            $insert_stmt->bindParam(':kelas', $kelas);
            $insert_stmt->bindParam(':session_id', $session_id);
            
            if ($insert_stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Player berhasil didaftarkan',
                    'session_id' => $session_id,
                    'player_id' => $this->conn->lastInsertId()
                ];
            } else {
                return ['success' => false, 'message' => 'Gagal mendaftarkan player'];
            }
        } catch (Exception $e) {
            error_log("Register player error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error server: ' . $e->getMessage()];
        }
    }

    // Update player progress
    public function updateProgress($session_id, $total_mistakes) {
        try {
            $update_query = "UPDATE " . $this->table_name . " 
                            SET total_mistakes = :total_mistakes, updated_at = CURRENT_TIMESTAMP 
                            WHERE session_id = :session_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':total_mistakes', $total_mistakes);
            $update_stmt->bindParam(':session_id', $session_id);
            
            if ($update_stmt->execute()) {
                return ['success' => true, 'message' => 'Progress updated'];
            } else {
                return ['success' => false, 'message' => 'Gagal update progress'];
            }
        } catch (Exception $e) {
            error_log("Update progress error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error server: ' . $e->getMessage()];
        }
    }

    // Complete game
    public function completeGame($session_id, $total_mistakes) {
        try {
            // Calculate score (100 - mistakes * 10, minimum 0)
            $score = max(0, 100 - ($total_mistakes * 10));
            
            $update_query = "UPDATE " . $this->table_name . " 
                            SET status = 'selesai', 
                                score = :score,
                                total_mistakes = :total_mistakes,
                                completion_time = CURRENT_TIMESTAMP,
                                updated_at = CURRENT_TIMESTAMP 
                            WHERE session_id = :session_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':score', $score);
            $update_stmt->bindParam(':total_mistakes', $total_mistakes);
            $update_stmt->bindParam(':session_id', $session_id);
            
            if ($update_stmt->execute()) {
                return [
                    'success' => true, 
                    'message' => 'Game completed!',
                    'score' => $score,
                    'rank' => $this->getPlayerRank($session_id)
                ];
            } else {
                return ['success' => false, 'message' => 'Gagal menyelesaikan game'];
            }
        } catch (Exception $e) {
            error_log("Complete game error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error server: ' . $e->getMessage()];
        }
    }

    // Get leaderboard
    public function getLeaderboard($limit = 50) {
        try {
            $query = "SELECT nama, kelas, status, score, total_mistakes, completion_time,
                             RANK() OVER (ORDER BY status DESC, score DESC, total_mistakes ASC, completion_time ASC) as ranking
                      FROM " . $this->table_name . " 
                      WHERE status = 'selesai'
                      ORDER BY score DESC, total_mistakes ASC, completion_time ASC 
                      LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'leaderboard' => $leaderboard,
                'total_players' => count($leaderboard)
            ];
        } catch (Exception $e) {
            error_log("Get leaderboard error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error server: ' . $e->getMessage()];
        }
    }

    // Get player rank
    private function getPlayerRank($session_id) {
        try {
            $query = "SELECT ranking FROM (
                        SELECT session_id, 
                               RANK() OVER (ORDER BY score DESC, total_mistakes ASC, completion_time ASC) as ranking
                        FROM " . $this->table_name . " 
                        WHERE status = 'selesai'
                      ) ranked 
                      WHERE session_id = :session_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['ranking'] : null;
        } catch (Exception $e) {
            error_log("Get player rank error: " . $e->getMessage());
            return null;
        }
    }

    // Get statistics
    public function getStats() {
        try {
            $query = "SELECT 
                        COUNT(*) as total_players,
                        COUNT(CASE WHEN status = 'selesai' THEN 1 END) as completed_players,
                        AVG(CASE WHEN status = 'selesai' THEN score END) as avg_score,
                        MIN(CASE WHEN status = 'selesai' THEN total_mistakes END) as best_mistakes
                      FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'stats' => $stats
            ];
        } catch (Exception $e) {
            error_log("Get stats error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error server: ' . $e->getMessage()];
        }
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$api = new LeaderboardAPI();

switch ($method) {
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $action = isset($input['action']) ? $input['action'] : '';
        
        switch ($action) {
            case 'register':
                if (!validateInput($input, ['nama', 'kelas'])) {
                    sendError('Nama dan kelas harus diisi');
                }
                $result = $api->registerPlayer(
                    trim($input['nama']), 
                    trim($input['kelas'])
                );
                sendResponse($result);
                break;
                
            case 'update_progress':
                if (!validateInput($input, ['session_id', 'total_mistakes'])) {
                    sendError('Session ID dan total mistakes harus diisi');
                }
                $result = $api->updateProgress(
                    $input['session_id'], 
                    (int)$input['total_mistakes']
                );
                sendResponse($result);
                break;
                
            case 'complete_game':
                if (!validateInput($input, ['session_id', 'total_mistakes'])) {
                    sendError('Session ID dan total mistakes harus diisi');
                }
                $result = $api->completeGame(
                    $input['session_id'], 
                    (int)$input['total_mistakes']
                );
                sendResponse($result);
                break;
                
            default:
                sendError('Action tidak valid');
        }
        break;
        
    case 'GET':
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($action) {
            case 'leaderboard':
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                $result = $api->getLeaderboard($limit);
                sendResponse($result);
                break;
                
            case 'stats':
                $result = $api->getStats();
                sendResponse($result);
                break;
                
            default:
                sendError('Action tidak valid');
        }
        break;
        
    default:
        sendError('Method tidak didukung', 405);
}
?>