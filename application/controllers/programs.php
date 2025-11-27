<?php
class Programs extends Controller {
    private const PROGRAMS_PER_PAGE = 3; // Match view's $itemsPerPage
    private const PROGRAMS_PER_ROW = 2;  // For layout logic if needed

    public function index() {
        // Pagination Logic
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * self::PROGRAMS_PER_PAGE;

        // Load models
        $settingModel = $this->model('SettingModel');
        $programModel = $this->model('ProgramModel');

        // Fetch Data
        $programsHeroKeys = [
            'programs_hero_title',
            'programs_hero_subtitle',
            'programs_hero_image'
        ];
        $programsHeroSettings = $settingModel->getMultiple($programsHeroKeys, '');
        $programsList = $programModel->getAllPrograms($offset, self::PROGRAMS_PER_PAGE);
        $totalPrograms = $programModel->getTotalProgramsCount();
        $totalPages = ceil($totalPrograms / self::PROGRAMS_PER_PAGE);

        // Validate page number
        if ($page > $totalPages && $totalPages > 0) {
            header("Location: " . BASE_URL . "/programs?page=" . $totalPages);
            exit;
        }

        // Prepare programs data to match view expectations
        $programs = array_map(function($program) {
            return [
                'id' => $program['id'],
                'title' => $program['title'],
                'image' => $program['image_filename'] ? 'programs/' . $program['image_filename'] : 'bg_5.jpg',
                'summary' => substr(strip_tags($program['content']), 0, 150) . (strlen(strip_tags($program['content'])) > 150 ? '...' : ''),
                'details' => $program['content'],
                'location' => 'Nationwide' // Hardcoded for now; add to DB if needed
            ];
        }, $programsList);

        // Prepare View Data
        $data = [
            'title' => htmlspecialchars($programsHeroSettings['programs_hero_title'] ?? 'Our Programs - Bantwana Initiative Eswatini'),
            'currentPage' => 'programs',
            'programsHero' => $programsHeroSettings,
            'programs' => $programs,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalPrograms' => $totalPrograms,
                'programsPerPage' => self::PROGRAMS_PER_PAGE
            ]
        ];

        // Load the view
        $this->view('programs/index', $data);
    }
}
?>