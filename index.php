<?php
// public_html/index.php

// 1. Define paths
// Define BASE_PATH ONCE in the entry point (index.php)
// It points to the project root (the parent directory of public_html)
define('BASE_PATH', dirname(__DIR__));
// Define BASE_URL - adjust the path if your project isn't directly in htdocs/bantwana
// Consider making this dynamic for production, but this works for local dev start
//define('BASE_URL', 'http://localhost/bantwana/public_html'); // <-- Adjust 'bantwana' if needed
define('BASE_URL', 'https://bantwana.org.sz/');
// 2. Load configuration
// config.php should NOT redefine BASE_PATH now
require_once BASE_PATH . '/application/config/config.php';

// 3. Autoload essential core classes
// A simple function to load core classes like Controller, Database
// This can be expanded or replaced with spl_autoload_register later
function loadCoreClass($className) {
    $filePath = BASE_PATH . '/application/core/' . $className . '.php';
    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        // Handle missing *core* class - these are critical
        // You might want a more graceful error handler/page in the future
        die("Critical core class '$className' not found at '$filePath'.");
    }
}

// --- SPECIAL CASE: Handle /admin route ---
// If the first part of the URL is 'admin' and there's no specific method,
// redirect to the main admin dashboard page.
if (isset($urlParts[0]) && strtolower($urlParts[0]) === 'admin' && (!isset($urlParts[1]) || empty($urlParts[1]))) {
     // Redirect to the admin dashboard PHP file directly
     header('Location: ' . BASE_URL . '/admin/pages/dashboard.php');
     exit;
}
// --- END SPECIAL CASE ---

// Load the essential base Controller class so controllers can extend it
loadCoreClass('Controller');
// Load the Database class if needed globally or for bootstrapping
loadCoreClass('Database'); // Uncomment if needed directly in index.php context

// 4. Simple Routing (Very Basic - Improve Later)
// Assume URL structure: http://localhost/bantwana/public_html/controller/method/param1/param2
// .htaccess should route requests to index.php?url=...

// Get the URL segment and sanitize it
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home'; // Default to 'home' controller
$urlParts = explode('/', filter_var($url, FILTER_SANITIZE_URL));

// Determine controller, method, and parameters
$controllerName = isset($urlParts[0]) ? ucfirst(strtolower($urlParts[0])) : 'Home'; // Capitalize first letter, ensure consistency
$methodName = isset($urlParts[1]) ? $urlParts[1] : 'index'; // Default method
$params = array_slice($urlParts, 2); // Remaining parts are parameters

// 5. Load Controller File
$controllerFile = BASE_PATH . '/application/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // 6. Instantiate Controller
    // Ensure the class name matches the filename (e.g., Home.php contains class Home)
    if (class_exists($controllerName)) {
        // Instantiate the controller
        // It should extend Controller, which was loaded earlier
        $controller = new $controllerName();

        // 7. Call Method
        if (method_exists($controller, $methodName)) {
            // Call the method with parameters
            call_user_func_array([$controller, $methodName], $params);
        } else {
            // Handle method not found (show 404 or error page)
            // Consider including a 404 view here later
            http_response_code(404); // Send 404 status
            die("Method '$methodName' not found in controller '$controllerName'.");
        }
    } else {
         // Handle class name mismatch or file inclusion issue
         die("Class '$controllerName' not found in file '$controllerFile'. Check class name and file name match.");
    }
} else {
    // Handle controller not found (show 404 or error page)
    http_response_code(404); // Send 404 status
    die("Controller '$controllerName' not found. File '$controllerFile' does not exist.");
}

?>
