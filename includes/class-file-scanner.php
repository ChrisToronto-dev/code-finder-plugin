<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * A RecursiveFilterIterator to exclude specific directories.
 */
class Code_Finder_Recursive_Filter_Iterator extends RecursiveFilterIterator {
    
    public static $excluded_paths = [
        'node_modules',
        'vendor',
        'build',
        'dist',
        'assets/images',
        'images',
        'lang',
        'languages',
        '.git',
        '.svn',
        'tests',
        'docs',
    ];

    public function accept(): bool {
        $file = $this->current();
        
        // If it's a directory, check if it's in the exclusion list
        if ($file->isDir()) {
            if (in_array($file->getFilename(), self::$excluded_paths)) {
                return false;
            }
        }
        
        return true;
    }
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
        $paths_to_scan = [];

        foreach ($directories as $directory_key) {
            switch ($directory_key) {
                case 'themes':
                    $paths_to_scan[] = get_stylesheet_directory(); // Child theme
                    $paths_to_scan[] = get_template_directory();  // Parent theme
                    break;
                case 'plugins':
                    $paths_to_scan[] = WP_PLUGIN_DIR;
                    break;
                case 'uploads':
                    $upload_dir = wp_upload_dir();
                    $paths_to_scan[] = $upload_dir['basedir'];
                    break;
            }
        }

        $paths_to_scan = array_unique($paths_to_scan);

        foreach ($paths_to_scan as $path) {
            if (!$path || !is_dir($path)) {
                continue;
            }

            try {
                $directory_iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
                $filter_iterator = new Code_Finder_Recursive_Filter_Iterator($directory_iterator);
                $iterator = new RecursiveIteratorIterator($filter_iterator, RecursiveIteratorIterator::LEAVES_ONLY);

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $extension = strtolower($file->getExtension());
                        if (in_array($extension, $this->allowed_extensions)) {
                            $real_path = $file->getRealPath();
                            if ($real_path) {
                                $files_found[] = $real_path;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // Log error or handle it gracefully, e.g., by skipping the problematic path
                // For now, we just continue to the next path.
                continue;
            }
        }

        return array_unique($files_found);
    }
}
