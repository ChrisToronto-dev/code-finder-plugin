<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Code_Finder_Code_Searcher {

    /**
     * Searches for a pattern within a given list of files and custom CSS, using a specific search type.
     *
     * @param string $term The term to search for.
     * @param array $files A list of absolute file paths to search within.
     * @param string $type The type of search to perform (e.g., 'any', 'css_class').
     * @return array A list of matching results.
     */
    public function search($term, $files, $type = 'any') {
        $results = [];
        $safe_term = preg_quote($term, '/');

        switch ($type) {
            case 'css_class':
                // Use multiple patterns to avoid complex character classes
                $patterns = [
                    '/\.' . $safe_term . '(?=[\s{\.:\[,>]|$)/i',  // .classname
                    '/#' . $safe_term . '(?=[\s{\.:\[,>]|$)/i',    // #idname
                    '/class\s*=\s*[""][^"\\]*\b' . $safe_term . '\b[^"\\]*["\\]/i' // class="...classname..."
                ];
                break;
                
            case 'js_function':
                $patterns = ['/(?:function\s+|const\s+|let\s+|var\s+)' . $safe_term . '\s*(?:\(|=|:)/i'];
                break;
                
            case 'js_variable':
                $patterns = ['/(?:var|let|const)\s+' . $safe_term . '(?:\s*=|\s*;|\s*,)/i'];
                break;
                
            case 'html_tag':
                $patterns = ['/<\/?(' . $safe_term . ')(?:\s[^>]*)?>/i'];
                break;
                
            case 'any':
            default:
                $patterns = ['/' . $safe_term . '/i'];
                break;
        }

        // Search in Customizer's Additional CSS
        $custom_css = wp_get_custom_css();
        if (!empty($custom_css)) {
            $lines = explode("\n", $custom_css);
            foreach ($lines as $line_number => $line) {
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $line)) {
                        $results[] = [
                            'file'         => __('Appearance > Customize > Additional CSS', 'code-finder'),
                            'line_number'  => $line_number + 1,
                            'line_content' => rtrim($line),
                        ];
                        break; // Found match, no need to check other patterns for this line
                    }
                }
            }
        }

        foreach ($files as $file) {
            if (!is_readable($file)) {
                continue;
            }

            $handle = fopen($file, 'r');
            if ($handle) {
                $line_number = 1;
                while (($line = fgets($handle)) !== false) {
                    foreach ($patterns as $pattern) {
                        if (preg_match($pattern, $line)) {
                            $results[] = [
                                'file'         => $file,
                                'line_number'  => $line_number,
                                'line_content' => rtrim($line),
                            ];
                            break; // Found match, no need to check other patterns for this line
                        }
                    }
                    $line_number++;
                }
                fclose($handle);
            }
        }

        return $results;
    }
}