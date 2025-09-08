-- Session-based system tables
-- Import this to add session functionality

-- Sessions table for invite links
CREATE TABLE IF NOT EXISTS sessions (
  id VARCHAR(32) NOT NULL PRIMARY KEY,
  room_slug VARCHAR(100) NOT NULL,
  created_by INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  max_users INT UNSIGNED DEFAULT 50,
  INDEX idx_sessions_room (room_slug),
  INDEX idx_sessions_created_by (created_by),
  CONSTRAINT fk_sessions_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Session participants (mahasiswa yang join via invite)
CREATE TABLE IF NOT EXISTS session_participants (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  session_id VARCHAR(32) NOT NULL,
  username VARCHAR(100) NOT NULL,
  joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_seen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_session_username (session_id, username),
  INDEX idx_session_participants_session (session_id),
  CONSTRAINT fk_session_participants_session FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;