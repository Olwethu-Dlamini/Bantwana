<?php
// public_html/admin_proxy.php
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$adminPath = str_replace('/admin/', '', $requestPath);
$filePath = dirname(__DIR__) . '/admin/' . $adminPath;

if (file_exists($filePath) && is_file($filePath)) {
    include $filePath;
} else {
    http_response_code(404);
    echo 'Admin file not found';
}
?>
