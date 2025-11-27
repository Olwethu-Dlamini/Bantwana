<?php
// application/controllers/AdminPagesController.php

class AdminPagesController extends Controller {

    public function index() {
        // Redirect to dashboard or show an error if not implemented yet
        $this->view('admin/dashboard/index');
    }

    public function edit($slug = null) {
        // Fetch the page data based on the slug
        $pageModel = new PageModel();
        $heroSectionModel = new HeroSectionModel();

        if ($slug === null) {
            // Handle invalid slug or redirect to dashboard
            header('Location: /admin/dashboard');
            exit;
        }

        $pageData = $pageModel->getPageBySlug($slug);
        $heroData = $heroSectionModel->getHeroSectionByPageSlug($slug);

        $data = [
            'title' => "Edit Page: {$slug}",
            'currentPage' => 'edit',
            'page' => $pageData,
            'heroSection' => $heroData,
        ];

        $this->view('admin/pages/edit', $data);
    }

    public function update($slug = null) {
        // Handle form submission for updating the page and hero section
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate input
            $pageModel = new PageModel();
            $heroSectionModel = new HeroSectionModel();

            $title = trim($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';
            $metaDescription = $_POST['meta_description'] ?? '';

            $heroHeading = trim($_POST['hero_heading'] ?? '');
            $heroSubheading = $_POST['hero_subheading'] ?? '';
            $heroButtonText = trim($_POST['hero_button_text'] ?? '');
            $heroButtonLink = trim($_POST['hero_button_link'] ?? '');

            // Update the page
            $pageModel->updatePage($slug, [
                'title' => $title,
                'content' => $content,
                'meta_description' => $metaDescription,
            ]);

            // Update the hero section
            $heroSectionModel->updateHeroSection($slug, [
                'heading' => $heroHeading,
                'subheading' => $heroSubheading,
                'button_text' => $heroButtonText,
                'button_link' => $heroButtonLink,
            ]);

            // Optionally, handle image upload
            // ...

            // Redirect back to the edit page with success message
            header("Location: /admin/pages/edit/$slug");
            exit;
        } else {
            // If accessed directly via GET, redirect to edit page
            header("Location: /admin/pages/edit/$slug");
            exit;
        }
    }
}
?>