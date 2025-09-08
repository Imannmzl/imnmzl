<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['user'])) {
	redirect('chat.php');
} else {
	redirect('login.php');
}

