<?php
// application/controllers/Publications.php

class Publications extends Controller {

    public function index() {
        // Load models
        $settingModel = $this->model('SettingModel');
        $publicationModel = $this->model('PublicationModel'); // Use the new PublicationModel

        // --- Fetch Hero Section Settings ---
        $heroContentKeys = [
            'publications_hero_title',
            'publications_hero_subtitle',
            'publications_hero_image'
        ];
        $publicationsHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
        // Provide defaults if settings are not found
        $publicationsHeroSettings['publications_hero_title'] = $publicationsHeroSettings['publications_hero_title'] ?? 'Our Publications';
        $publicationsHeroSettings['publications_hero_subtitle'] = $publicationsHeroSettings['publications_hero_subtitle'] ?? 'Access our reports, manuals, and resources.';
        $publicationsHeroSettings['publications_hero_image'] = $publicationsHeroSettings['publications_hero_image'] ?? 'bg_5.jpg'; // Default image

        // --- Fetch All Publications ---
        $allPublications = $publicationModel->getAllPublications(); // Fetch flat list

        // --- Dynamically Categorize Publications based on DB values ---
        $categorizedPublications = [];
        $allCategories = []; // Optional: To get a sorted list of unique categories

        foreach ($allPublications as $publication) {
            $category = $publication['category'] ?? 'Other'; // Use 'Other' as a fallback if category is null/empty
            $normalizedCategory = trim($category); // Normalize whitespace if needed

            // Initialize the array for this category if it doesn't exist yet
            if (!isset($categorizedPublications[$normalizedCategory])) {
                $categorizedPublications[$normalizedCategory] = [];
                $allCategories[] = $normalizedCategory; // Add to list of categories
            }

            // Add the publication to its category group
            $categorizedPublications[$normalizedCategory][] = $publication;
        }

        // Sort the categories alphabetically if you want a consistent order in the view
        // You might want to sort them differently based on priority later if needed
        sort($allCategories);

        // --- Sort Publications Within Each Category ---
        foreach ($categorizedPublications as &$categoryGroup) {
             usort($categoryGroup, function($a, $b) {
                 // Example: Sort by sort_order DESC (higher first), then by ID DESC
                 $orderA = $a['sort_order'] ?? 0;
                 $orderB = $b['sort_order'] ?? 0;
                 if ($orderA == $orderB) {
                     return $b['id'] <=> $a['id']; // Secondary sort by ID descending
                 }
                 return $orderB <=> $orderA; // Primary sort by sort_order descending
             });
        }
        unset($categoryGroup); // Break the reference

        // --- End Categorization ---

        // Prepare data for the view
        $data = [
            'currentPage' => 'publications', // For highlighting nav link
            'publicationsHero' => $publicationsHeroSettings, // Pass hero settings
            'categorizedPublications' => $categorizedPublications, // Pass categorized publications
            'allCategories' => $allCategories // Optional: Pass sorted list of categories
        ];

        // Load the view
        $this->view('publications/index', $data);
    }
}
?>