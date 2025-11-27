<?php
// application/controllers/Donate.php

class Donate extends Controller {

    public function index() {
        // Load models
        $settingModel = $this->model('SettingModel'); // Still used for general settings if needed
        $donateModel = $this->model('DonateModel');   // Use the new DonateModel

        // --- Fetch Donate Page Hero Settings ---
        $heroContentKeys = [
            'donate_hero_title',
            'donate_hero_subtitle',
            'donate_hero_image'
        ];
        $donateHeroSettings = $donateModel->getMultiple($heroContentKeys, '');
        // Provide defaults if settings are not found
        $donateHeroSettings['donate_hero_title'] = $donateHeroSettings['donate_hero_title'] ?? 'Invest in Our Future';
        $donateHeroSettings['donate_hero_subtitle'] = $donateHeroSettings['donate_hero_subtitle'] ?? 'Transparent, accountable, and impactful giving.';
        $donateHeroSettings['donate_hero_image'] = $donateHeroSettings['donate_hero_image'] ?? 'bg_5.jpg'; // Default image

        // --- Fetch Main Content Settings ---
        $mainContentKeys = [
            'donate_main_heading',
            'donate_main_subheading',
            'donate_main_content'
        ];
        $donateMainSettings = $donateModel->getMultiple($mainContentKeys, '');
        // Provide defaults if settings are not found
        $donateMainSettings['donate_main_heading'] = $donateMainSettings['donate_main_heading'] ?? 'Your Donation at Work';
        $donateMainSettings['donate_main_subheading'] = $donateMainSettings['donate_main_subheading'] ?? 'Transparency and Accountability';
        $donateMainSettings['donate_main_content'] = $donateMainSettings['donate_main_content'] ?? '<p>We are committed to using your generous support effectively and efficiently to maximize our impact on the lives of vulnerable children and families.</p>';

        // Prepare data for the view
        $data = [
            'title' => htmlspecialchars(($donateHeroSettings['donate_hero_title'] ?? 'Invest in Our Future') . ' - Bantwana Initiative Eswatini'),
            'currentPage' => 'donate', // For highlighting nav link
            'donateHero' => $donateHeroSettings, // Pass hero settings
            'donateMain' => $donateMainSettings  // Pass main content settings
        ];

        // Load the view
        $this->view('donate/index', $data);
    }
}
?>