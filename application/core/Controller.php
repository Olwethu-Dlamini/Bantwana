<?php
class Controller {
    /**
     * Load a model class
     * @param string $model The model name (e.g., 'ProgramModel')
     * @return object Instance of the model class
     * @throws Exception If model file is not found
     */
    public function model($model) {
        $modelFile = BASE_PATH . '/application/models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            if (class_exists($model)) {
                return new $model();
            } else {
                $error = "Class '$model' not found in '$modelFile'.";
                error_log($error);
                throw new Exception($error);
            }
        } else {
            $error = "Model file '$model' not found at '$modelFile'.";
            error_log($error);
            throw new Exception($error);
        }
    }

    /**
     * Render a view with an optional layout
     * @param string $view The view path (e.g., 'programs/index')
     * @param array $data Data to pass to the view
     * @param string|null $layout The layout file path (e.g., 'layouts/main') or null for no layout
     * @return void
     * @throws Exception If view or layout file is not found
     */
    public function view($view, $data = [], $layout = 'layouts/main') {
        // Normalize view path
        $viewFile = BASE_PATH . '/application/views/' . $view . '.php';
        
        // Start output buffering for view content
        ob_start();
        if (file_exists($viewFile)) {
            // Extract data with a prefix to avoid variable collisions
            extract($data, EXTR_PREFIX_SAME, 'data_');
            require $viewFile;
            $viewContent = ob_get_clean();
        } else {
            ob_end_clean();
            $error = "View '$view' not found at '$viewFile'.";
            error_log($error);
            throw new Exception($error);
        }

        // Render layout if specified
        if ($layout !== null && $layout !== false) {
            $layoutFile = BASE_PATH . '/application/views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                // Make view content and data available to the layout
                $content = $viewContent;
                extract($data, EXTR_PREFIX_SAME, 'data_');
                require $layoutFile;
            } else {
                error_log("Layout '$layout' not found at '$layoutFile'. Falling back to view content.");
                echo $viewContent; // Fallback to view content
            }
        } else {
            echo $viewContent; // No layout, output view directly
        }
    }

    /**
     * Redirect to a URL
     * @param string $url The URL to redirect to
     * @param int $statusCode HTTP status code (e.g., 301, 302)
     * @return void
     */
    public function redirect($url, $statusCode = 302) {
        header("Location: $url", true, $statusCode);
        exit;
    }
}
?>