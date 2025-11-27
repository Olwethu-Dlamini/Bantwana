<?php
// application/controllers/Volunteer.php

class Volunteer extends Controller {

    public function index() {
        $data = [
            'title' => 'Volunteer - Bantwana Initiative Eswatini',
            'currentPage' => 'volunteer' // Useful if you add a top-level nav item later
        ];
        $this->view('volunteer/index', $data);
    }

    // Optional: Method for handling the signup form submission
    // public function signup() {
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         // Process form data (sanitize, validate)
    //         $name = trim($_POST['volunteer_name'] ?? '');
    //         $email = trim($_POST['volunteer_email'] ?? '');
    //         $message = trim($_POST['volunteer_message'] ?? '');
    //
    //         // Basic validation
    //         if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //              // Redirect back with error or display error on page
    //              // For now, just reload the form page
    //              // You might want to pass error data back
    //              header('Location: ' . BASE_URL . '/volunteer');
    //              exit;
    //         }
    //
    //         // Process the data (e.g., send email, save to database)
    //         // Example: Send email
    //         $to = "info@bantwana.co.sz"; // Or get from config
    //         $subject = "New Volunteer Signup from Website";
    //         $body = "Name: $name\nEmail: $email\nMessage:\n$message";
    //         $headers = "From: $email";
    //
    //         if (mail($to, $subject, $body, $headers)) {
    //             // Success - maybe redirect to a thank you page
    //             // For now, just reload the page with a success flag or message
    //             // You could set a session variable or pass data differently
    //             // header('Location: ' . BASE_URL . '/volunteer?success=1');
    //             // exit;
    //         } else {
    //             // Error handling
    //         }
    //         // For simplicity, just reload the page for now
    //         header('Location: ' . BASE_URL . '/volunteer');
    //         exit;
    //     } else {
    //         // If accessed via GET, redirect to the form page
    //         header('Location: ' . BASE_URL . '/volunteer');
    //         exit;
    //     }
    // }
}
?>