-- Database Table untuk Leaderboard Hutan Harmoni
-- Database: n1567943_mediainteraktif_db

CREATE TABLE IF NOT EXISTS `leaderboard_himpunan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `status` enum('selesai','belum_selesai') NOT NULL DEFAULT 'belum_selesai',
  `score` int(11) DEFAULT 0,
  `total_mistakes` int(11) DEFAULT 0,
  `completion_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_session` (`nama`, `kelas`, `session_id`),
  KEY `idx_status` (`status`),
  KEY `idx_kelas` (`kelas`),
  KEY `idx_completion_time` (`completion_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index untuk performa
CREATE INDEX idx_leaderboard ON leaderboard_himpunan (status, score DESC, total_mistakes ASC, completion_time ASC);