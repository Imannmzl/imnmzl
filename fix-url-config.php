<?php
/**
 * 🔧 Fix URL Configuration - Chat Room Realtime
 * Script untuk memperbaiki konfigurasi URL yang salah
 */

// Start output buffering
ob_start();

// Get current URL information
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Calculate correct base URL
$pathParts = explode('/', trim($scriptName, '/'));
array_pop($pathParts); // Remove current file
$basePath = '/' . implode('/', $pathParts);
$basePath = rtrim($basePath, '/');

$correctBaseUrl = $protocol . '://' . $host . $basePath;

// Fix config.php
$configFile = 'config/config.php';
$configContent = file_get_contents($configFile);

// Update APP_URL if it's wrong
$oldAppUrl = "define('APP_URL', 'http://localhost/Chat-Room-Realtime');";
$newAppUrl = "define('APP_URL', '$correctBaseUrl');";

if (strpos($configContent, $oldAppUrl) !== false) {
    $configContent = str_replace($oldAppUrl, $newAppUrl, $configContent);
    file_put_contents($configFile, $configContent);
    $configUpdated = true;
} else {
    $configUpdated = false;
}

// Create a simple redirect test
$testRedirects = [
    'teacher' => $correctBaseUrl . '/dashboard/teacher/index.php',
    'student' => $correctBaseUrl . '/dashboard/student/index.php',
    'login' => $correctBaseUrl . '/login.php'
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix URL Configuration - Chat Room Realtime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .config-info { background: #f8f9fa; padding: 1rem; border-radius: 8px; font-family: monospace; }
        .test-url { margin: 0.5rem 0; padding: 0.5rem; background: white; border-radius: 4px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>🔧 Fix URL Configuration</h4>
                        <small class="text-muted">Memperbaiki konfigurasi URL yang salah</small>
                    </div>
                    <div class="card-body">
                        
                        <h5>📍 Current URL Information</h5>
                        <div class="config-info">
                            <div class="test-url">
                                <strong>Protocol:</strong> <?php echo $protocol; ?>
                            </div>
                            <div class="test-url">
                                <strong>Host:</strong> <?php echo $host; ?>
                            </div>
                            <div class="test-url">
                                <strong>Request URI:</strong> <?php echo $requestUri; ?>
                            </div>
                            <div class="test-url">
                                <strong>Script Name:</strong> <?php echo $scriptName; ?>
                            </div>
                            <div class="test-url">
                                <strong>Calculated Base URL:</strong> <span class="success"><?php echo $correctBaseUrl; ?></span>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">🔧 Configuration Fixes</h5>
                        <div class="config-info">
                            <div class="test-url">
                                <strong>Config.php Update:</strong> 
                                <?php if ($configUpdated): ?>
                                    <span class="success">✅ Updated APP_URL to: <?php echo $correctBaseUrl; ?></span>
                                <?php else: ?>
                                    <span class="warning">⚠️ No changes needed</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">🧪 Test Correct URLs</h5>
                        <div class="config-info">
                            <div class="test-url">
                                <strong>Teacher Dashboard:</strong> 
                                <a href="<?php echo $testRedirects['teacher']; ?>" target="_blank" class="btn btn-sm btn-primary">Test</a>
                                <br><small><?php echo $testRedirects['teacher']; ?></small>
                            </div>
                            <div class="test-url">
                                <strong>Student Dashboard:</strong> 
                                <a href="<?php echo $testRedirects['student']; ?>" target="_blank" class="btn btn-sm btn-primary">Test</a>
                                <br><small><?php echo $testRedirects['student']; ?></small>
                            </div>
                            <div class="test-url">
                                <strong>Login Page:</strong> 
                                <a href="<?php echo $testRedirects['login']; ?>" target="_blank" class="btn btn-sm btn-primary">Test</a>
                                <br><small><?php echo $testRedirects['login']; ?></small>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">🔍 Debug Information</h5>
                        <div class="config-info">
                            <div class="test-url">
                                <strong>Server Variables:</strong>
                                <pre><?php 
                                $serverVars = [
                                    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A',
                                    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
                                    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
                                    'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'N/A',
                                    'HTTPS' => isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'N/A',
                                    'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? 'N/A'
                                ];
                                echo json_encode($serverVars, JSON_PRETTY_PRINT);
                                ?></pre>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="login.php" class="btn btn-primary">Go to Login</a>
                            <a href="force-clear-cache.html" class="btn btn-warning">Force Clear Cache</a>
                            <a href="debug-url.php" class="btn btn-outline-secondary">Debug URL Info</a>
                            <button onclick="window.location.reload(true)" class="btn btn-outline-primary">Reload Page</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Log current page info
        console.log('=== URL FIX DEBUG INFO ===');
        console.log('Window Location:', window.location);
        console.log('Document URL:', document.URL);
        console.log('Base URI:', document.baseURI);
        
        // Check for any stored redirect URLs
        const localStorageKeys = Object.keys(localStorage);
        const sessionStorageKeys = Object.keys(sessionStorage);
        
        console.log('LocalStorage keys:', localStorageKeys);
        console.log('SessionStorage keys:', sessionStorageKeys);
        
        // Look for redirect-related data
        const redirectData = [...localStorageKeys, ...sessionStorageKeys].filter(key => 
            key.toLowerCase().includes('redirect') || 
            key.toLowerCase().includes('url') || 
            key.toLowerCase().includes('location') ||
            key.toLowerCase().includes('multinteraktif')
        );
        
        if (redirectData.length > 0) {
            console.warn('Found redirect-related data:', redirectData);
            redirectData.forEach(key => {
                const value = localStorage.getItem(key) || sessionStorage.getItem(key);
                console.log(`${key}:`, value);
            });
        }
        
        // Test if we can access the correct URLs
        console.log('Testing correct URLs...');
        fetch('<?php echo $testRedirects['login']; ?>')
            .then(response => {
                console.log('Login URL test:', response.status, response.url);
            })
            .catch(error => {
                console.error('Login URL test failed:', error);
            });
    </script>
</body>
</html>