<?php
// admin/login.php

session_start();

// --- Autoload core classes (simple example) ---
// Ensure the path is correct relative to admin/login.php
// This assumes admin is at the same level as application
define('BASE_PATH', dirname(__DIR__)); // Points to C:\xampp\htdocs\bantwana

// Load configuration
require_once BASE_PATH . '/application/config/config.php';

// Autoload function for core classes (like Database, UserModel)
function loadAdminClass($className) {
    // Check core directory first
    $coreFile = BASE_PATH . '/application/core/' . $className . '.php';
    if (file_exists($coreFile)) {
        require_once $coreFile;
        return;
    }

    // Check models directory
    $modelFile = BASE_PATH . '/application/models/' . $className . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }

    // Handle missing class
    error_log("Required admin class '$className' not found at expected locations.");
    // die("Critical error: Class $className not found."); // Or handle more gracefully
}

// Load essential classes
loadAdminClass('Database');
loadAdminClass('UserModel');

// --- Initialize ---
$error_message = '';
$success_message = '';

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

// --- Process Login Form Submission ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = trim($_POST['username'] ?? ''); // Can be username or email
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($identifier) || empty($password)) {
        $error_message = 'Please enter both username/email and password.';
    } else {
        // Instantiate the UserModel
        $userModel = new UserModel();

        // Attempt to find the user
        $user = $userModel->findByUsernameOrEmail($identifier);

        if ($user && password_verify($password, $user['password'])) {
            // Success: Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Store username in session

            // --- Log successful login ---
            $logMessage = "Admin {$user['username']} (ID: {$user['id']}) logged in successfully from IP: " . $_SERVER['REMOTE_ADDR'] . " at " . date('Y-m-d H:i:s');
            logActivity($logMessage);

            header('Location: dashboard.php');
            exit;
        } else {
            // Failure: Invalid credentials
            $error_message = 'Invalid username/email or password.';
            // --- Log failed login attempt ---
            $logMessage = "Failed login attempt for identifier: '$identifier' from IP: " . $_SERVER['REMOTE_ADDR'] . " at " . date('Y-m-d H:i:s');
            logActivity($logMessage);
        }
    }
}
$success_message = '';
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success_message = 'You have been successfully logged out.';
}

// --- Function to log admin activities ---
function logActivity($message) {
    // Create a new database connection specifically for logging
    // This avoids potential issues with the one used in UserModel during login process
    try {
        $logDb = new Database();
        $stmt = $logDb->pdo->prepare('INSERT INTO logs (timestamp, message) VALUES (NOW(), ?)');
        $stmt->execute([$message]);
    } catch (Exception $e) {
        // If logging fails, don't break the main login flow, just log the error
        error_log("CMS Log Error: Failed to write log entry - " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bantwana Initiative Eswatini</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            background-color: #f0f0f1; /* Light grey background similar to WP */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; /* WP font stack */
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%; /* Responsive width */
            padding: 20px;
            background-color: #fff;
            border-radius: 4px; /* Slightly rounded corners */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13); /* Subtle shadow */
            margin: 40px auto; /* Margin for smaller screens */
        }
        .login-heading {
             margin-bottom: 20px;
             text-align: center;
             color: #1d2327; /* Dark text */
             font-size: 24px;
             font-weight: 400; /* Normal weight */
        }
        .form-group label {
            font-weight: 600; /* Semi-bold labels */
            color: #1d2327;
            margin-bottom: 5px; /* Space below label */
            display: block; /* Ensure label takes full width */
        }
        .form-control {
            border: 1px solid #8c8f94; /* Border color */
            border-radius: 4px;
            padding: 10px 12px; /* Padding */
            font-size: 16px; /* Font size */
            line-height: 1.5; /* Line height */
            min-height: 40px; /* Minimum height */
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05); /* Inner shadow */
        }
        .form-control:focus {
            border-color: #2271b1; /* Blue border on focus */
            box-shadow: 0 0 0 1px #2271b1; /* Focus ring */
            outline: 2px solid transparent; /* Remove default outline */
        }
        .btn-primary {
            background-color: #2271b1 !important; /* Primary button color */
            border-color: #2271b1 !important; /* Primary button border */
            color: #fff !important; /* White text */
            padding: 10px 16px; /* Button padding */
            font-size: 16px; /* Font size */
            line-height: 1.5; /* Line height */
            border-radius: 4px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor */
            width: 100%; /* Full width button */
            height: 40px; /* Consistent height */
        }
        .btn-primary:hover {
            background-color: #135e96 !important; /* Darker blue on hover */
            border-color: #135e96 !important; /* Darker border on hover */
        }
        .form-check-input {
            margin-top: 6px; /* Align checkbox */
        }
        .form-check-label {
            margin-left: 8px; /* Space between checkbox and label */
            font-weight: normal; /* Normal weight for checkbox label */
        }
        .forgot-password-link, .back-to-site-link, .privacy-policy-link {
            text-align: center;
            margin-top: 15px; /* Space above links */
        }
        .forgot-password-link a,
        .back-to-site-link a,
        .privacy-policy-link a {
            color: #2271b1; /* Link color */
            text-decoration: none; /* Remove underline */
            font-size: 14px; /* Smaller font size */
        }
        .forgot-password-link a:hover,
        .back-to-site-link a:hover,
        .privacy-policy-link a:hover {
            color: #135e96; /* Darker link color on hover */
            text-decoration: underline; /* Underline on hover */
        }
        .privacy-policy-link {
            margin-top: 25px; /* More space for privacy link */
            font-size: 12px; /* Even smaller font size */
            color: #50575e; /* Muted text color */
        }
        .privacy-policy-link a {
            color: #50575e; /* Muted link color */
        }
        .alert {
            border-radius: 4px; /* Rounded corners for alerts */
            padding: 12px 16px; /* Padding */
            margin-bottom: 20px; /* Space below alert */
        }
        .alert-danger {
            background-color: #fcf0f1; /* Light red background */
            border-left: 4px solid #d63638; /* Red left border */
            color: #8a2424; /* Dark red text */
        }
        .alert-success {
            background-color: #edfaef; /* Light green background */
            border-left: 4px solid #00a32a; /* Green left border */
            color: #004514; /* Dark green text */
        }
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-container {
                padding: 15px;
                margin: 20px;
            }
            .login-heading {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <h2 class="login-heading">Bantwana CMS</h2> <!-- Simplified heading -->

                    <!-- Display Error Message -->
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    <!-- Display Success Message (e.g., after logout) -->
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="form-group">
                            <label for="username">Username or Email Address</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                            <label class="form-check-label" for="rememberMe">Remember Me</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Log In</button>
                    </form>
                    <!-- End Login Form -->

                    <!-- Forgot Password Link -->
                    <div class="forgot-password-link">
                        <a href="#">Lost your password?</a>
                    </div>

                    <!-- Go Back Link -->
                    <div class="back-to-site-link">
                        <a href="<?php echo BASE_URL; ?>/">&larr; Go to Bantwana</a>
                    </div>

                    <!-- Privacy Policy Link -->
                    <div class="privacy-policy-link">
                        <a href="#">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (optional for basic form, but good to include if using other BS features) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>
</html>