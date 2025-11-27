<?php
// application/controllers/Contact.php

class Contact extends Controller {

    public function index() {
        // Load models
        $settingModel = $this->model('SettingModel');

        // --- Fetch Hero Section Settings ---
        $heroContentKeys = [
            'contact_hero_title',
            'contact_hero_subtitle',
            'contact_hero_image'
        ];
        $contactHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
        // Provide defaults if settings are not found
        $contactHeroSettings['contact_hero_title'] = $contactHeroSettings['contact_hero_title'] ?? 'Get In Touch';
        $contactHeroSettings['contact_hero_subtitle'] = $contactHeroSettings['contact_hero_subtitle'] ?? "We'd love to hear from you. Reach out with any questions or comments.";
        $contactHeroSettings['contact_hero_image'] = $contactHeroSettings['contact_hero_image'] ?? 'bg_7.jpg'; // Default image

        // --- Fetch All Programs (if needed for the page) ---
        // $programModel = $this->model('ProgramModel');
        // $programs = $programModel->getAllPrograms();

        // Prepare data for the view
        $data = [
            'title' => htmlspecialchars(($contactHeroSettings['contact_hero_title'] ?? 'Get In Touch') . ' - Bantwana Initiative Eswatini'),
            'currentPage' => 'contact', // For highlighting nav link
            'contactHero' => $contactHeroSettings, // Pass hero settings
            // 'programs' => $programs // Pass programs if needed
        ];

        // Load the view
        $this->view('contact/index', $data);
    }

}
?>