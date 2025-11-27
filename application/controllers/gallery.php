<?php
// application/controllers/Gallery.php

class Gallery extends Controller {

    public function index() {
        // Load the SettingModel to get the hero image
        $settingModel = $this->model('SettingModel');
        // Load the GalleryModel to get galleries and images
        $galleryModel = $this->model('GalleryModel');

        // Fetch the gallery hero image setting
        $galleryHeroImage = $settingModel->get('gallery_hero_image', 'bg_6.jpg'); // Default if not set

        // Fetch all galleries with their (preview) images using the new method
        $galleries = $galleryModel->getAllGalleriesWithImages(6); // Limit to 6 images per gallery for preview

        // Prepare data for the view
        $data = [
            'title' => 'Photo Gallery - Bantwana Initiative Eswatini',
            'currentPage' => 'gallery', // For highlighting nav link
            'galleries' => $galleries,
            'galleryHeroImage' => $galleryHeroImage // Pass hero image filename to view
        ];

        // Load the view
        $this->view('gallery/index', $data);
    }
    

    // Optional: Method for a single gallery view (e.g., /gallery/view/slug)
    // public function view($slug) {
    //     $galleryModel = $this->model('GalleryModel');
    //     $settingModel = $this->model('SettingModel'); // If you need settings here too
    //
    //     $gallery = $galleryModel->getGalleryBySlug($slug);
    //
    //     if (!$gallery) {
    //          http_response_code(404);
    //          $this->view('errors/404', ['title' => 'Gallery Not Found', 'currentPage' => 'gallery']);
    //          return;
    //     }
    //
    //     $images = $galleryModel->getImagesByGalleryId($gallery['id']); // Get ALL images for single view
    //
    //     $data = [
    //         'title' => $gallery['name'] . ' - Photo Gallery',
    //         'currentPage' => 'gallery',
    //         'gallery' => $gallery,
    //         'images' => $images,
    //         'galleryHeroImage' => $gallery['name'] . ' Hero Image' // Or fetch specific setting if exists
    //     ];
    //
    //     $this->view('gallery/view_single', $data); // You'd need to create this view file
    // }
}
?>