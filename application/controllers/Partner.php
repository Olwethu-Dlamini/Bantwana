<?php
// application/controllers/Partner.php

class Partner extends Controller {

    public function index() {
        // Load models
        $settingModel = $this->model('SettingModel');

        // --- Fetch Hero Section Settings ---
        $heroContentKeys = [
            'partner_hero_title',
            'partner_hero_subtitle',
            'partner_hero_image'
        ];
        $partnerHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
        // Provide defaults if settings are not found
        $partnerHeroSettings['partner_hero_title'] = $partnerHeroSettings['partner_hero_title'] ?? 'Partner With Us';
        $partnerHeroSettings['partner_hero_subtitle'] = $partnerHeroSettings['partner_hero_subtitle'] ?? 'Join forces to create sustainable change for vulnerable children and families.';
        $partnerHeroSettings['partner_hero_image'] = $partnerHeroSettings['partner_hero_image'] ?? 'bg_5.jpg'; // Default image

        
        // Prepare data for the view
        $data = [
            'title' => htmlspecialchars(($partnerHeroSettings['partner_hero_title'] ?? 'Partner With Us') . ' - Bantwana Initiative Eswatini'),
            'currentPage' => 'partner', // For highlighting nav link
            'partnerHero' => $partnerHeroSettings, // Pass hero settings
            // 'programs' => $programs // Pass programs if needed
        ];

        // Load the view
        $this->view('partner/index', $data);
    }
}
?>