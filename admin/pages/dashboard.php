<?php
// admin/dashboard.php
session_start();

// --- Authentication Check ---
// Ensure the user is logged in. If not, redirect to login.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Optionally log failed access attempt? (Though less common for direct page access)
    header('Location: login.php');
    exit;
}

// --- Get User Info (Optional, for display) ---
$current_user = $_SESSION['username']; // Or fetch full details from DB if needed

// --- Page Title ---
$page_title = "Dashboard - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background-color: #f0f0f1; /* Light grey background */
        }
        .admin-header {
            background-color: #2271b1; /* WP-like blue */
            color: white;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            font-size: 1.5em;
            margin: 0;
            font-weight: 400;
        }
        .admin-header .user-info {
            font-size: 0.9em;
        }
        .admin-main {
            max-width: 1200px; /* Limit width for large screens */
            margin: 0 auto;
            padding: 0 20px 20px 20px;
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
        }
        .card-header {
            background-color: #f6f7f7;
            border-bottom: 1px solid #ccd0d4;
            font-weight: 600;
            padding: 10px 15px;
        }
        .card-body {
            padding: 15px;
        }
        .card-body ul {
            list-style: none;
            padding-left: 0;
        }
        .card-body li {
            margin-bottom: 8px;
        }
        .card-body a {
            text-decoration: none;
            color: #2271b1; /* Link color */
        }
        .card-body a:hover {
            color: #135e96; /* Darker link color on hover */
            text-decoration: underline;
        }
        .footer-info {
            text-align: center;
            margin-top: 30px;
            color: #50575e;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>Bantwana CMS</h1>
        <div class="user-info">
            Howdy, <a href="#" style="color: inherit; text-decoration: underline;"><?php echo htmlspecialchars($current_user); ?></a> |
            <a href="logout.php" style="color: inherit; text-decoration: underline;">Log Out</a>
        </div>
    </div>
    <!-- End Admin Header -->

    <div class="admin-main">
        <h2 class="mb-4">Dashboard</h2>

        <!-- Welcome Message -->
        <div class="alert alert-info" role="alert">
            Welcome to the Bantwana CMS, <?php echo htmlspecialchars($current_user); ?>!
        </div>

        <!-- Main Dashboard Content -->
        <div class="row">
            <!-- Manage Content Card -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Manage Content</div>
                    <div class="card-body">
                        <ul>
                            <li><a href="pages/home_edit.php">Edit Homepage (Hero/Counter)</a></li>
                            <li><a href="pages/about_edit.php">Edit About/Who are We Page</a></li>
                            <li><a href="pages/gallery_edit.php">Manage Gallery</a></li>
                             <li><a href="pages/programs_manage.php">Edit Programs Page</a></li>
                            <li><a href="pages/team_edit.php">Edit Team Page Hero</a></li>
                            <li><a href="pages/donate_edit.php">Edit Donate Page</a></li>
                            <li><a href="pages/blog_manage.php">Edit Blog Page</a></li>
                            <li><a href="pages/publications_manage.php">Manage Publications</a></li> <!-- Add this line -->
                            <li><a href="pages/volunteer_manage.php">Manage Volunteers</a></li> <!-- Add this line -->
                            <li><a href="pages/internship_edit.php">Edit Internships Page Hero</a></li>
                            <li><a href="pages/careers_manage.php">Edit Careers Page Hero</a></li>
                            <li><a href="pages/partner_edit.php">Edit Partner Page</a></li> 
                            <li><a href="pages/contact_manage.php">Edit Contact Page</a></li> 
                        </ul>
                    </div>
                </div>
            </div>

            
        <!-- End Main Dashboard Content -->

        <!-- Footer Info -->
        <div class="footer-info">
            <p>Bantwana Initiative Eswatini CMS</p>
        </div>
        <!-- End Footer Info -->
    </div>

    <!-- Bootstrap JS and dependencies (optional for basic layout, but good to include if using other BS features) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>
</html>