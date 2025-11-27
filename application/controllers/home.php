<?php
// application/controllers/Home.php

class Home extends Controller {

    public function index() {
        // Load the SettingModel
        $settingModel = $this->model('SettingModel');
         // Load the ProgramModel to get programs for the homepage
        $programModel = $this->model('ProgramModel');

        // Define the keys for homepage content we need
        $homeContentKeys = [
            'home_hero_title',
            'home_hero_subtitle',
            'home_hero_image', // <-- Add this key
            'home_counter_main_text',
            'home_counter_number',
            'home_counter_unit',
            'home_counter_donate_title',
            'home_counter_donate_text',
            'home_counter_volunteer_title',
            'home_counter_volunteer_text',
            'programs_hero_title',
            'programs_hero_subtitle',
            'programs_hero_image'
        ];

        // Fetch the settings
        $homeSettings = $settingModel->getMultiple($homeContentKeys, ''); // Default to empty string if not found

        // --- Fetch Latest Programs for Homepage ---
        // Get all programs and slice the first 3 (or however many you want)
        // Alternatively, add a method like getLatestPrograms($limit) to ProgramModel
        $allPrograms = $programModel->getAllPrograms();
        $latestPrograms = array_slice($allPrograms, 0, 3); // Get first 3 programs
        // --- End Fetch Latest Programs ---

        // Prepare data for the view
        $data = [
            'title' => 'Bantwana Initiative Eswatini', // Default title, could also be a setting
            'currentPage' => 'home',
            // Pass the fetched settings to the view
            'homeContent' => $homeSettings,
            // Add other data as needed...
            'latestPrograms' => $latestPrograms // Pass the sliced programs array
        ];

        // Load the view
        $this->view('home/index', $data);
    }

    // ... (other methods like fetchPosts if needed) ...
}
?>
