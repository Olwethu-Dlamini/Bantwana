<?php
session_start();

// --- Authentication Check ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

// --- Define Paths for Subdomain ---
define('ADMIN_BASE_PATH', dirname($_SERVER['DOCUMENT_ROOT'])); // Points to /home/bantwana
if (!defined('BASE_PATH')) {
    define('BASE_PATH', ADMIN_BASE_PATH);
}

// --- Define BASE_URL for Main Domain ---
if (!defined('BASE_URL')) {
    $host = $_SERVER['HTTP_HOST'];
    $mainHost = str_replace('admin.', '', $host);
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('BASE_URL', $protocol . '://' . $mainHost);
}

// --- Autoload Function ---
function loadAdminClass($className) {
    $coreFile = BASE_PATH . '/application/core/' . $className . '.php';
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }
    $modelFile = BASE_PATH . '/application/models/' . $className . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
    error_log("Required admin class '$className' not found at expected locations ($coreFile or $modelFile).");
}

// --- Load Configuration ---
$configPath = BASE_PATH . '/application/config/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    die("Configuration error: Unable to load application config.");
}

// Load essential classes
loadAdminClass('Database');
loadAdminClass('SettingModel');
?>