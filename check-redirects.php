<?php
/**
 * 🔍 Check Redirects - Chat Room Realtime
 * Script untuk memeriksa dan memperbaiki semua redirect
 */

require_once 'config/config.php';
require_once 'config/auth-hybrid.php';

// Get current user info
$currentUser = getCurrentUser();
$isLoggedIn = isLoggedIn();

// Test redirect URLs
$testUrls = [
    'login' => BASE_URL . '/login.php',
    'teacher_dashboard' => BASE_URL . '/dashboard/teacher/index.php',
    'student_dashboard' => BASE_URL . '/dashboard/student/index.php',
    'register' => BASE_URL . '/register.php'
];

// Check if URLs are accessible
$urlTests = [];
foreach ($testUrls as $name => $url) {
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'timeout' => 5
        ]
    ]);
    
    $headers = @get_headers($url, 1, $context);
    $urlTests[$name] = [
        'url' => $url,
        'accessible' => $headers && strpos($headers[0], '200') !== false,
        'status' => $headers ? $headers[0] : 'No response'
    ];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Redirects - Chat Room Realtime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .url-test { margin: 0.5rem 0; padding: 0.5rem; border-radius: 4px; }
        .success { background: #d4edda; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>🔍 Check Redirects & URLs</h4>
                        <small class="text-muted">Memeriksa semua URL dan redirect dalam aplikasi</small>
                    </div>
                    <div class="card-body">
                        
                        <h5>👤 Current User Status</h5>
                        <div class="url-test info">
                            <strong>Logged In:</strong> <?php echo $isLoggedIn ? 'Yes' : 'No'; ?><br>
                            <?php if ($isLoggedIn && $currentUser): ?>
                                <strong>User:</strong> <?php echo htmlspecialchars($currentUser['name']); ?><br>
                                <strong>Role:</strong> <?php echo htmlspecialchars($currentUser['role']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($currentUser['email']); ?>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="mt-4">🌐 URL Accessibility Tests</h5>
                        <?php foreach ($urlTests as $name => $test): ?>
                            <div class="url-test <?php echo $test['accessible'] ? 'success' : 'error'; ?>">
                                <strong><?php echo ucwords(str_replace('_', ' ', $name)); ?>:</strong><br>
                                <small><?php echo $test['url']; ?></small><br>
                                <strong>Status:</strong> <?php echo $test['status']; ?>
                                <?php if ($test['accessible']): ?>
                                    <span class="badge bg-success">✅ Accessible</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">❌ Not Accessible</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <h5 class="mt-4">🔧 Configuration Information</h5>
                        <div class="url-test info">
                            <strong>BASE_URL:</strong> <?php echo BASE_URL; ?><br>
                            <strong>APP_URL:</strong> <?php echo APP_URL; ?><br>
                            <strong>Current Script:</strong> <?php echo $_SERVER['SCRIPT_NAME'] ?? 'N/A'; ?><br>
                            <strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?><br>
                            <strong>HTTP Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'N/A'; ?><br>
                            <strong>Protocol:</strong> <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP'; ?>
                        </div>
                        
                        <h5 class="mt-4">🧪 Test Redirects</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if ($isLoggedIn && $currentUser): ?>
                                <?php if ($currentUser['role'] === 'teacher'): ?>
                                    <a href="<?php echo $testUrls['teacher_dashboard']; ?>" class="btn btn-primary">Test Teacher Dashboard</a>
                                <?php else: ?>
                                    <a href="<?php echo $testUrls['student_dashboard']; ?>" class="btn btn-primary">Test Student Dashboard</a>
                                <?php endif; ?>
                                <a href="api/auth.php?action=logout" class="btn btn-warning">Logout</a>
                            <?php else: ?>
                                <a href="<?php echo $testUrls['login']; ?>" class="btn btn-primary">Test Login</a>
                                <a href="<?php echo $testUrls['register']; ?>" class="btn btn-outline-primary">Test Register</a>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="mt-4">🔍 JavaScript Redirect Test</h5>
                        <div class="url-test info">
                            <button id="testJsRedirect" class="btn btn-info">Test JavaScript Redirect Logic</button>
                            <div id="jsTestResult" class="mt-2"></div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="login.php" class="btn btn-primary">Go to Login</a>
                            <a href="fix-url-config.php" class="btn btn-warning">Fix URL Config</a>
                            <a href="force-clear-cache.html" class="btn btn-danger">Force Clear Cache</a>
                            <a href="debug-url.php" class="btn btn-outline-secondary">Debug URL Info</a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('testJsRedirect').addEventListener('click', function() {
            const resultDiv = document.getElementById('jsTestResult');
            resultDiv.innerHTML = '<div class="text-info">Testing JavaScript redirect logic...</div>';
            
            // Test the redirect logic from hybrid-auth.js
            const origin = window.location.origin;
            const pathname = window.location.pathname;
            
            // Get the base path by removing current file/directory
            const pathParts = pathname.split('/').filter(part => part);
            let basePath = '';
            
            if (pathParts.length > 0) {
                // Remove the current file/directory from path
                const dirParts = pathParts.slice(0, -1);
                basePath = '/' + dirParts.join('/');
            }
            
            const baseUrl = origin + basePath;
            
            const testUrls = {
                teacher: baseUrl + '/dashboard/teacher/index.php',
                student: baseUrl + '/dashboard/student/index.php',
                login: baseUrl + '/login.php'
            };
            
            let result = '<div class="mt-2">';
            result += '<strong>Calculated Base URL:</strong> ' + baseUrl + '<br>';
            result += '<strong>Teacher Dashboard:</strong> ' + testUrls.teacher + '<br>';
            result += '<strong>Student Dashboard:</strong> ' + testUrls.student + '<br>';
            result += '<strong>Login:</strong> ' + testUrls.login + '<br>';
            result += '</div>';
            
            // Test if URLs are accessible
            fetch(testUrls.login)
                .then(response => {
                    result += '<div class="mt-2"><strong>Login URL Test:</strong> ' + response.status + ' ' + response.statusText + '</div>';
                    resultDiv.innerHTML = result;
                })
                .catch(error => {
                    result += '<div class="mt-2 text-danger"><strong>Login URL Test Error:</strong> ' + error.message + '</div>';
                    resultDiv.innerHTML = result;
                });
        });
        
        // Log current page info
        console.log('=== REDIRECT CHECK DEBUG ===');
        console.log('Current URL:', window.location.href);
        console.log('Origin:', window.location.origin);
        console.log('Pathname:', window.location.pathname);
        
        // Check for any stored data that might cause wrong redirects
        const allKeys = [...Object.keys(localStorage), ...Object.keys(sessionStorage)];
        const redirectKeys = allKeys.filter(key => 
            key.toLowerCase().includes('redirect') || 
            key.toLowerCase().includes('url') || 
            key.toLowerCase().includes('location') ||
            key.toLowerCase().includes('multinteraktif')
        );
        
        if (redirectKeys.length > 0) {
            console.warn('Found potentially problematic stored data:', redirectKeys);
            redirectKeys.forEach(key => {
                const value = localStorage.getItem(key) || sessionStorage.getItem(key);
                console.log(`${key}:`, value);
            });
        }
    </script>
</body>
</html>