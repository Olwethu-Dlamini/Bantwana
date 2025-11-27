<?php
// admin/pages/edit.php
session_start();

// --- Authentication Check ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

// --- Include necessary files ---
define('BASE_PATH', dirname(dirname(__DIR__)));
require_once BASE_PATH . '/application/config/config.php';
require_once BASE_PATH . '/application/core/Database.php';

// --- Get Page Slug from URL ---
$slug = $_GET['slug'] ?? '';

// --- Initialize variables ---
$page_data = null;
$error_message = '';
$success_message = '';
$form_title = '';
$form_content = '';
$form_meta_description = '';

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($slug)) {
    $form_title = trim($_POST['title'] ?? '');
    $form_content = $_POST['content'] ?? ''; // Don't trim content as it might contain HTML formatting
    $form_meta_description = trim($_POST['meta_description'] ?? '');

    // Basic validation
    if (empty($form_title)) {
        $error_message = 'Page title is required.';
    } else {
        // Update the page in the database
        try {
            $db = new Database();
            $sql = "UPDATE pages SET title = :title, content = :content, meta_description = :meta_description, updated_at = NOW() WHERE slug = :slug";
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute([
                ':title' => $form_title,
                ':content' => $form_content,
                ':meta_description' => $form_meta_description,
                ':slug' => $slug
            ]);

            if ($stmt->rowCount() > 0) {
                $success_message = 'Page updated successfully.';
                // Log the activity
                $logMessage = "Admin {$_SESSION['username']} updated page '{$slug}' at " . date('Y-m-d H:i:s');
                logPageActivity($logMessage, $db);
            } else {
                // No rows affected, might mean the slug didn't exist or data was identical
                $success_message = 'No changes were made or page not found.';
            }

            // Fetch the updated data to repopulate the form
            $stmt_select = $db->pdo->prepare("SELECT title, content, meta_description FROM pages WHERE slug = :slug");
            $stmt_select->execute([':slug' => $slug]);
            $page_data = $stmt_select->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("CMS Admin - Error updating page '{$slug}': " . $e->getMessage());
            $error_message = 'An error occurred while saving the page. Please try again.';
        }
    }
}

// --- Fetch Page Data (for initial display or after failed save) ---
if (!empty($slug) && $page_data === null) { // Only fetch if slug is provided and data wasn't fetched during POST
    try {
        $db = new Database(); // Re-use or create new DB connection
        $stmt = $db->pdo->prepare("SELECT title, content, meta_description FROM pages WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        $page_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$page_data) {
            $error_message = 'Page not found.';
        } else {
            // Populate form variables with data from DB for initial display
            $form_title = $page_data['title'];
            $form_content = $page_data['content'];
            $form_meta_description = $page_data['meta_description'];
        }
    } catch (PDOException $e) {
        error_log("CMS Admin - Error fetching page '{$slug}': " . $e->getMessage());
        $error_message = 'An error occurred while loading the page. Please try again.';
    }
}

// --- Page Title ---
$page_title = "Edit Page - Bantwana CMS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Include a simple WYSIWYG editor like TinyMCE or Trumbowyg, or just use a textarea -->
    <!-- For simplicity, we'll use a plain textarea for now. You can integrate a rich text editor later. -->
    <style>
         /* Basic admin styles */
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; background-color: #f0f0f1; }
        .admin-header { background-color: #2271b1; color: white; padding: 15px 20px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { font-size: 1.5em; margin: 0; font-weight: 400; }
        .admin-header .user-info { font-size: 0.9em; }
        .admin-main { max-width: 1200px; margin: 0 auto; padding: 0 20px 20px 20px; }
        .card { margin-bottom: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04); }
        .card-header { background-color: #f6f7f7; border-bottom: 1px solid #ccd0d4; font-weight: 600; padding: 10px 15px; }
        .card-body { padding: 15px; }
        .form-group label { font-weight: 600; }
        .alert { border-radius: 4px; }
        textarea.form-control { min-height: 300px; font-family: 'Courier New', Courier, monospace; } /* Monospace for HTML content */
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
                <li class="breadcrumb-item"><a href="index.php">Manage Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Page</li>
            </ol>
        </nav>

        <h2 class="mb-4">Edit Page: <?php echo htmlspecialchars($slug); ?></h2>

        <!-- Display Messages -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($slug) || !empty($error_message) && strpos($error_message, 'not found') !== false): ?>
            <div class="alert alert-warning" role="alert">
                Invalid page request or page not found.
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    Page Content
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug); ?>">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($form_title); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="meta_description">Meta Description (SEO):</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?php echo htmlspecialchars($form_meta_description); ?></textarea>
                            <small class="form-text text-muted">A brief summary for search engines (usually 150-160 characters).</small>
                        </div>
                        <div class="form-group">
                            <label for="content">Content:</label>
                            <textarea class="form-control" id="content" name="content" required><?php echo htmlspecialchars($form_content); ?></textarea>
                            <small class="form-text text-muted">You can use HTML tags for formatting.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Page</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optional: Add a WYSIWYG editor script here if integrating one -->
</body>
</html>
<?php
// --- Function to log page activity ---
function logPageActivity($message, $databaseConnection) {
    try {
        $stmt = $databaseConnection->pdo->prepare('INSERT INTO logs (timestamp, message) VALUES (NOW(), ?)');
        $stmt->execute([$message]);
    } catch (Exception $e) {
        error_log("CMS Page Edit Log Error: " . $e->getMessage());
    }
}
?>