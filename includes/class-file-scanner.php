<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Code_Finder_File_Scanner {

    /**
     * The allowed file extensions to scan.
     *
     * @var array
     */
    private $allowed_extensions = ['php', 'css', 'js', 'html', 'scss'];

    /**
     * Scans specified directories for files with allowed extensions.
     *
     * @param array $directories The directories to scan (e.g., ['themes', 'plugins']).
     * @return array A list of absolute file paths.
     */
    public function scan_files($directories = []) {
        $files_found = [];
        $base_path = WP_CONTENT_DIR;

        $scan_paths = [
            'themes'  => $base_path . '/themes/',
            'plugins' => $base_path . '/plugins/',
            'uploads' => $base_path . '/uploads/',
        ];

        foreach ($directories as $directory_key) {
            $path = $scan_paths[$directory_key] ?? null;

            if (!$path || !is_dir($path)) {
                continue;
            }

            $directory_iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($directory_iterator, RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, $this->allowed_extensions)) {
                        $files_found[] = $file->getRealPath();
                    }
                }
            }
        }

        return $files_found;
    }
}
