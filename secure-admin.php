<?php
// File: /public_html/admin.php (or /public_html/secure-admin.php for better security)
session_start();

// Security: You might want to add IP restrictions here
// $allowed_ips = ['192.168.1.39'];
// if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//     die('Access denied');
// }

// Determine which admin page to load
$page = $_GET['page'] ?? 'login';
$admin_root = dirname(__DIR__) . '/admin/';

// Authentication check (except for login page)
if ($page !== 'login' && !isset($_SESSION['admin_logged_in'])) {
    header('Location: /admin.php?page=login');
    exit;
}

// Route to appropriate admin page
switch($page) {
    case 'login':
        if (isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin.php?page=dashboard');
            exit;
        }
        include $admin_root . 'login.php';
        break;
        
    case 'logout':
        include $admin_root . 'logout.php';
        break;
        
    case 'dashboard':
        include $admin_root . 'dashboard.php';
        break;
        
    // Pages management
    case 'about_edit':
        include $admin_root . 'pages/about_edit.php';
        break;
        
    case 'blog_manage':
        include $admin_root . 'pages/blog_manage.php';
        break;
        
    case 'careers_manage':
        include $admin_root . 'pages/careers_manage.php';
        break;
        
    case 'contact_manage':
        include $admin_root . 'pages/contact_manage.php';
        break;
        
    case 'donate_edit':
        include $admin_root . 'pages/donate_edit.php';
        break;
        
    case 'gallery_edit':
        include $admin_root . 'pages/gallery_edit.php';
        break;
        
    case 'home_edit':
        include $admin_root . 'pages/home_edit.php';
        break;
        
    case 'internship_edit':
        include $admin_root . 'pages/internship_edit.php';
        break;
        
    case 'partner_edit':
        include $admin_root . 'pages/partner_edit.php';
        break;
        
    case 'programs_edit':
        include $admin_root . 'pages/programs_edit.php';
        break;
        
    case 'programs_manage':
        include $admin_root . 'pages/programs_manage.php';
        break;
        
    case 'publications_manage':
        include $admin_root . 'pages/publications_manage.php';
        break;
        
    case 'team_edit':
        include $admin_root . 'pages/team_edit.php';
        break;
        
    case 'volunteer_manage':
        include $admin_root . 'pages/volunteer_manage.php';
        break;
        
    default:
        // 404 or redirect to dashboard
        header('Location: /admin.php?page=dashboard');
        exit;
}
?>