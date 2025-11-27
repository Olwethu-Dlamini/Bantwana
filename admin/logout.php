<?php
// admin/logout.php
session_start();

// --- Log the logout activity ---
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // Include database connection if you want to log this in the DB
    // For simplicity here, we'll just use error_log, but you can adapt the logActivity function from login.php
    // Ensure BASE_PATH is defined or adjust the path accordingly
    define('BASE_PATH', dirname(__DIR__));
    require_once BASE_PATH . '/application/core/Database.php'; // Adjust path if needed

    function logActivitySimple($message) {
        try {
            $logDb = new Database();
            $stmt = $logDb->pdo->prepare('INSERT INTO logs (timestamp, message) VALUES (NOW(), ?)');
            $stmt->execute([$message]);
        } catch (Exception $e) {
            error_log("CMS Logout Log Error: " . $e->getMessage());
        }
    }

    $logMessage = "Admin {$_SESSION['username']} (ID: {$_SESSION['user_id']}) logged out at " . date('Y-m-d H:i:s');
    logActivitySimple($logMessage);
}

// Destroy all session data
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page with a success message
// Using a session for the message requires the session to be active, which it isn't after destroy.
// A common way is to use a query parameter or set a new session just for the redirect.
// Let's use a query parameter for simplicity here.
$redirect_url = 'login.php?logout=success';
// Alternatively, if you want to use sessions for messages, restart a minimal session just for this:
// session_start();
// $_SESSION['success_message'] = 'You have been successfully logged out.';
// session_write_close();
// $redirect_url = 'login.php';

header("Location: $redirect_url");
exit;
?>