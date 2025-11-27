<?php
// admin/pages/index.php
session_start();

// --- Authentication Check ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

include BASE_PATH . '/application/config/config.php';
include BASE_PATH . '/admin/pages/header.php';


// --- Fetch Pages from Database ---
try {
    $db = new Database();
    $stmt = $db->pdo->query("SELECT id, slug, title, updated_at FROM pages ORDER BY title ASC");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("CMS Admin - Error fetching pages: " . $e->getMessage());
    $pages = []; // Set to empty array on error
    $error_message = "Failed to load pages. Please check the logs.";
}

// --- Page Title ---
$page_title = "Manage Pages - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Basic admin styles - can be moved to a separate CSS file later */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; background-color: #f0f0f1; }
        .admin-header { background-color: #2271b1; color: white; padding: 15px 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { font-size: 1.5em; margin: 0; font-weight: 400; }
        .admin-header .user-info { font-size: 0.9em; }
        .admin-main { max-width: 1200px; margin: 0 auto; padding: 0 20px 20px 20px; }
        .card { margin-bottom: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04); }
        .card-header { background-color: #f6f7f7; border-bottom: 1px solid #ccd0d4; font-weight: 600; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 15px; }
        .table th { border-top: none; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .alert { border-radius: 4px; }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>Bantwana CMS</h1>
        <div class="user-info">
            Howdy, <a href="#" style="color: inherit; text-decoration: underline;"><?php echo htmlspecialchars($_SESSION['username']); ?></a> |
            <a href="../logout.php" style="color: inherit; text-decoration: underline;">Log Out</a>
        </div>
    </div>
    <!-- End Admin Header -->

    <div class="admin-main">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage Pages</li>
            </ol>
        </nav>

        <h2 class="mb-4">Manage Pages</h2>

        <!-- Display Error Message -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <span>All Pages</span>
                <!-- <a href="create.php" class="btn btn-primary btn-sm">Add New Page</a> --> <!-- Optional: Add create page functionality later -->
            </div>
            <div class="card-body">
                <?php if (empty($pages)): ?>
                    <p>No pages found in the database.</p>
                    <p><em>Make sure the 'pages' table exists and has content, or add pages via phpMyAdmin or a future 'Create Page' feature.</em></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">Slug</th>
                                    <th scope="col">Last Updated</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($page['title']); ?></td>
                                        <td><code><?php echo htmlspecialchars($page['slug']); ?></code></td>
                                        <td><?php echo htmlspecialchars($page['updated_at']); ?></td>
                                        <td>
                                            <a href="edit.php?slug=<?php echo urlencode($page['slug']); ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <!-- <a href="delete.php?id=<?php echo $page['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this page?')">Delete</a> --> <!-- Optional: Add delete functionality later -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>