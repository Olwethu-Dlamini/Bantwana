<?php
// application/controllers/Team.php

class Team extends Controller {

    public function index() {
        // Load models
        $settingModel = $this->model('SettingModel');
        // Load TeamMemberModel if using database for team members
        // $teamMemberModel = $this->model('TeamMemberModel');

        // --- Fetch Hero Section Settings ---
        $heroContentKeys = [
            'team_hero_title',
            'team_hero_subtitle',
            'team_hero_image'
        ];
        $teamHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
        // Provide defaults if settings are not found
        $teamHeroSettings['team_hero_title'] = $teamHeroSettings['team_hero_title'] ?? 'Our Team';
        $teamHeroSettings['team_hero_subtitle'] = $teamHeroSettings['team_hero_subtitle'] ?? 'Meet the dedicated individuals driving our mission.';
        $teamHeroSettings['team_hero_image'] = $teamHeroSettings['team_hero_image'] ?? 'bg_team.jpg'; // Default image

        // --- Fetch Thulani Earnshaw's Image Setting ---
        $thulaniImageSetting = $settingModel->get('team_thulani_image', 'thulani_earnshaw.jpg'); // Default image
        // --- End Fetch Thulani Image Setting ---

        // --- Fetch Team Members (from database or mock data) ---
        // Option 1: From Database (Recommended)
        // $teamMembers = $teamMemberModel->getAllActiveTeamMembers();

        // Option 2: Mock Data (if not using database yet)
        $teamMembers = [
            // ... (your existing mock data or logic) ...
        ];

        // Prepare data for the view
        $data = [
            'title' => htmlspecialchars($teamHeroSettings['team_hero_title'] . ' - Bantwana Initiative Eswatini'),
            'currentPage' => 'team', // For highlighting nav link
            'teamHero' => $teamHeroSettings,
            'teamMembers' => $teamMembers,
            'thulaniImage' => $thulaniImageSetting // Pass Thulani's image filename
        ];

        // Load the view
        $this->view('team/index', $data);
    }
}
?>