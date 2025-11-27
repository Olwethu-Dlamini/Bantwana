<?php
// Define base path for admin
define('ADMIN_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
define('APP_ROOT', ADMIN_ROOT . '../application/'); // Points to application folder

// Include necessary files
require_once APP_ROOT . 'core/Database.php';
require_once APP_ROOT . 'models/AdminUserModel.php';
// Add other required includes
?>