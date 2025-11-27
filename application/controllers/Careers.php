<?php
class Careers extends Controller {

    public function index() {
        $settingModel = $this->model('SettingModel');
        $jobModel = $this->model('JobModel');

        $heroContentKeys = ['careers_hero_title', 'careers_hero_subtitle', 'careers_hero_image'];
        $careersHeroSettings = $settingModel->getMultiple($heroContentKeys, '');
        $careersHeroSettings['careers_hero_title'] = $careersHeroSettings['careers_hero_title'] ?? 'Build Your Career With Us';
        $careersHeroSettings['careers_hero_subtitle'] = $careersHeroSettings['careers_hero_subtitle'] ?? 'Join a team passionate about creating lasting change for children and families.';
        $careersHeroSettings['careers_hero_image'] = $careersHeroSettings['careers_hero_image'] ?? 'bg_1.jpg';

        $activeJobs = $jobModel->getAllActiveJobs();

        $data = [
            'title' => htmlspecialchars(($careersHeroSettings['careers_hero_title'] ?? 'Build Your Career With Us') . ' - Bantwana Initiative Eswatini'),
            'currentPage' => 'careers',
            'careersHero' => $careersHeroSettings,
            'jobs' => $activeJobs
        ];

        $this->view('careers/index', $data);
    }
}