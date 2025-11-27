<?php
// application/controllers/About.php

class About extends Controller {

    public function index() {
        // Load the SettingModel
        $settingModel = $this->model('SettingModel');

        // Define the keys for About page content we need
        $aboutContentKeys = [
            // Hero Section
            'about_hero_title',
            'about_hero_image',
            // Story Section
            'about_history_image',
            'about_story_heading',
            'about_story_text_1',
            'about_story_text_2',
            'about_story_text_3',
            // Mission/Vision
            'about_mission_title',
            'about_mission_text',
            'about_vision_title',
            'about_vision_text',
            // Approach Section
            'about_approach_title',
            'about_approach_intro',
            // Approach Items (1-6)
            'about_approach_item_1_number', 'about_approach_item_1_heading', 'about_approach_item_1_text',
            'about_approach_item_2_number', 'about_approach_item_2_heading', 'about_approach_item_2_text',
            'about_approach_item_3_number', 'about_approach_item_3_heading', 'about_approach_item_3_text',
            'about_approach_item_4_number', 'about_approach_item_4_heading', 'about_approach_item_4_text',
            'about_approach_item_5_number', 'about_approach_item_5_heading', 'about_approach_item_5_text',
            'about_approach_item_6_number', 'about_approach_item_6_heading', 'about_approach_item_6_text',
            // Values Section
            'about_values_title',
            'about_values_intro',
            // Stats Section
            'about_stats_title',
            'about_stats_children_number', 'about_stats_children_text',
            'about_stats_staff_number', 'about_stats_staff_text',
            'about_stats_regions_number', 'about_stats_regions_text',
            'about_stats_years_number', 'about_stats_years_text',
        ];

        // Fetch the settings
        $aboutSettings = $settingModel->getMultiple($aboutContentKeys, ''); // Default to empty string if not found

        $data = [
            'title' => 'About Us - Bantwana Initiative Eswatini',
            'currentPage' => 'about',
            'aboutContent' => $aboutSettings // Pass the fetched settings to the view
        ];

        $this->view('about/index', $data);
    }

    // Add other methods if needed...
}
?>