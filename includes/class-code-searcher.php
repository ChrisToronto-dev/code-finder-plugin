<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Code_Finder_Code_Searcher {

    /**
     * Searches for a pattern within a given list of files, using a specific search type.
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
                // Re-ordered character class in lookahead to avoid escaping issues with ']'
                $regex = "/[.'"]" . $safe_term . "(?=[]\s.'",:]|$)/i";
                break;
            case 'js_function':
                $regex = '/(?:function\s+|const\s+)' . $safe_term . '\s*(?:\(|\=|:)/i';
                break;
            case 'js_variable':
                $regex = '/(?:var|let|const)\s+' . $safe_term . '\s*=/i';
                break;
            case 'html_tag':
                $regex = '/<\/?' . $safe_term . '[\s>]/i';
                break;
            case 'any':
            default:
                // Case-insensitive search for any text
                $regex = '/' . $safe_term . '/i';
                break;
        }

        foreach ($files as $file) {
            if (!is_readable($file)) {
                continue;
            }

            $handle = fopen($file, 'r');
            if ($handle) {
                $line_number = 1;
                while (($line = fgets($handle)) !== false) {
                    if (preg_match($regex, $line)) {
                        $results[] = [
                            'file'         => $file,
                            'line_number'  => $line_number,
                            'line_content' => rtrim($line),
                        ];
                    }
                    $line_number++;
                }
                fclose($handle);
            }
        }

        return $results;
    }
}
