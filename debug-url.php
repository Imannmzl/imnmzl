<?php
/**
 * 🔍 Debug URL Script - Chat Room Realtime
 * Script untuk debugging masalah URL dan redirect
 */

require_once 'config/config.php';
require_once 'config/auth-hybrid.php';

$debugInfo = [
    'current_url' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'N/A',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'base_url' => defined('BASE_URL') ? BASE_URL : 'Not defined',
    'app_url' => defined('APP_URL') ? APP_URL : 'Not defined',
    'protocol' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http',
    'user_logged_in' => isLoggedIn(),
    'current_user' => getCurrentUser(),
    'session_data' => $_SESSION ?? []
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug URL - Chat Room Realtime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .debug-info { background: #f8f9fa; padding: 1rem; border-radius: 8px; font-family: monospace; }
        .debug-item { margin: 0.5rem 0; padding: 0.5rem; background: white; border-radius: 4px; }
        pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>🔍 Debug URL Information</h4>
                        <small class="text-muted">Informasi untuk debugging masalah redirect URL</small>
                    </div>
                    <div class="card-body">
                        
                        <h5>📍 Server Information</h5>
                        <div class="debug-info">
                            <?php foreach ($debugInfo as $key => $value): ?>
                                <div class="debug-item">
                                    <strong><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</strong>
                                    <pre><?php echo is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : htmlspecialchars($value ?? 'NULL'); ?></pre>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <h5 class="mt-4">🧪 Test URLs</h5>
                        <div class="debug-info">
                            <div class="debug-item">
                                <strong>Dashboard Teacher URL:</strong>
                                <pre><?php echo BASE_URL; ?>/dashboard/teacher/index.php</pre>
                            </div>
                            <div class="debug-item">
                                <strong>Dashboard Student URL:</strong>
                                <pre><?php echo BASE_URL; ?>/dashboard/student/index.php</pre>
                            </div>
                            <div class="debug-item">
                                <strong>Login URL:</strong>
                                <pre><?php echo BASE_URL; ?>/login.php</pre>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">🔧 Actions</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="login.php" class="btn btn-primary">Go to Login</a>
                            <?php if (isLoggedIn()): ?>
                                <a href="api/auth.php?action=logout" class="btn btn-warning">Logout</a>
                                <?php 
                                $user = getCurrentUser();
                                if ($user): ?>
                                    <?php if ($user['role'] === 'teacher'): ?>
                                        <a href="dashboard/teacher/index.php" class="btn btn-success">Teacher Dashboard</a>
                                    <?php else: ?>
                                        <a href="dashboard/student/index.php" class="btn btn-success">Student Dashboard</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="clear-cache.html" class="btn btn-secondary">Clear Cache</a>
                        </div>
                        
                        <hr>
                        
                        <h5>📋 Browser Information</h5>
                        <div id="browserInfo" class="debug-info">
                            <div class="debug-item">
                                <strong>Current URL:</strong>
                                <pre id="currentUrl"></pre>
                            </div>
                            <div class="debug-item">
                                <strong>Origin:</strong>
                                <pre id="origin"></pre>
                            </div>
                            <div class="debug-item">
                                <strong>Pathname:</strong>
                                <pre id="pathname"></pre>
                            </div>
                            <div class="debug-item">
                                <strong>User Agent:</strong>
                                <pre id="userAgent"></pre>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fill browser information
        document.getElementById('currentUrl').textContent = window.location.href;
        document.getElementById('origin').textContent = window.location.origin;
        document.getElementById('pathname').textContent = window.location.pathname;
        document.getElementById('userAgent').textContent = navigator.userAgent;
        
        // Log debug info to console
        console.log('=== DEBUG URL INFORMATION ===');
        console.log('Window Location:', window.location);
        console.log('Document URL:', document.URL);
        console.log('Base URI:', document.baseURI);
        console.log('Document Domain:', document.domain);
        
        // Check localStorage and sessionStorage
        console.log('LocalStorage:', localStorage);
        console.log('SessionStorage:', sessionStorage);
        
        // Check for any stored redirect URLs
        const storedRedirect = localStorage.getItem('redirectUrl') || sessionStorage.getItem('redirectUrl');
        if (storedRedirect) {
            console.log('Found stored redirect URL:', storedRedirect);
        }
    </script>
</body>
</html>