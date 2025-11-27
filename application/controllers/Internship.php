<?php
// application/controllers/Internships.php

class Internship extends Controller {

    public function index() {
        // Load models
        $settingModel = $this->model('SettingModel'); // Still used for general settings if needed

        // --- Fetch Hero Section Settings ---
        $heroContentKeys = [
            'internships_hero_title',
            'internships_hero_subtitle',
            'internships_hero_image'
        ];
        $internshipsHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
        // Provide defaults if settings are not found
        $internshipsHeroSettings['internships_hero_title'] = $internshipsHeroSettings['internships_hero_title'] ?? 'Gain Experience, Make an Impact';
        $internshipsHeroSettings['internships_hero_subtitle'] = $internshipsHeroSettings['internships_hero_subtitle'] ?? 'Apply your academic knowledge in a real-world development setting.';
        $internshipsHeroSettings['internships_hero_image'] = $internshipsHeroSettings['internships_hero_image'] ?? 'bg_2.jpg'; // Default image

        // Prepare data for the view
        $data = [
            'title' => htmlspecialchars(($internshipsHeroSettings['internships_hero_title'] ?? 'Gain Experience, Make an Impact') . ' - Bantwana Initiative Eswatini'),
            'currentPage' => 'internships', // For highlighting nav link
            'internshipsHero' => $internshipsHeroSettings // Pass hero settings
        ];

        // Load the view
        $this->view('internship/index', $data);
    }
}
?>