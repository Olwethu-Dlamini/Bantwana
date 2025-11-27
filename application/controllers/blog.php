<?php
// application/controllers/blog.php

class Blog extends Controller {

    public function index() {
        // Data to pass to the view and layout
        $data = [
            'title' => 'Blog - Bantwana Initiative Eswatini',
            'currentPage' => 'blog' // Used in the layout to highlight the active nav link
        ];

        // Load the specific page content view within the main layout
        $this->view('blog/index', $data);
    }

    // You can add other methods if you need sub-pages, e.g., /about/team
    // public function team() { ... }
}
?>