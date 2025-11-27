<?php
// application/controllers/get_involved.php

class Get_Involved extends Controller {

    public function index() {
        // Data to pass to the view and layout
        $data = [
            'title' => 'Get Involved - Bantwana Initiative Eswatini',
            'currentPage' => 'get_involved' // Used in the layout to highlight the active nav link
        ];

        // Load the specific page content view within the main layout
        $this->view('get_involved/index', $data);
    }

    // You can add other methods if you need sub-pages, e.g., /about/team
    // public function team() { ... }
}
?>