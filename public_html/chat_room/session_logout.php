<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

// Clear session user
unset($_SESSION['session_user']);

// Redirect to join page
redirect('join.php');
?>