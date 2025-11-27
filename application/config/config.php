<?php
// Define base path and URL (basic example, consider environment detection)
//define('BASE_PATH', dirname(dirname(__DIR__))); // Points to the project root (outside public_html)
//define('BASE_URL', 'http://localhost/bantwana/public_html'); 

define('BASE_URL', 'https://bantwana.org.sz/');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'bantwana_bantwana'); 
define('DB_PASS', '8HA9pvB@Y8!v6l');     
define('DB_NAME', 'bantwana_db'); 

// Error reporting (Set to 0 for production)
ini_set('display_errors', 0); // Change to 0 for production
error_reporting(E_ALL);
ini_set('log_errors', 1);
// Autoloader or include paths (optional but helpful)
// set_include_path(get_include_path() . PATH_SEPARATOR . BASE_PATH . '/application/core');
?>
