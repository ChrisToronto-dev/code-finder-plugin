<?php
/**
 * Plugin Name:       Code Finder
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Search for CSS classes, JavaScript functions/variables, and HTML elements within your WordPress themes, plugins, and uploads.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       code-finder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_code_finder() {
    // Activation code here.
}
register_activation_hook(__FILE__, 'activate_code_finder');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_code_finder() {
    // Deactivation code here.
}
register_deactivation_hook(__FILE__, 'deactivate_code_finder');

/**
 * Add the top-level admin menu page.
 */
function code_finder_admin_page() {
    add_menu_page(
        __('Code Finder', 'code-finder'),
        __('Code Finder', 'code-finder'),
        'manage_options',
        'code-finder',
        'code_finder_page_html',
        'dashicons-search'
    );
}
add_action('admin_menu', 'code_finder_admin_page');

/**
 * Callback function to display the admin page content.
 */
function code_finder_page_html() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Include the admin page template
    require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';
}

/**
 * Enqueue scripts and styles for the admin page.
 */
function code_finder_enqueue_admin_scripts($hook) {
    // Only load on our plugin page
    if ('toplevel_page_code-finder' !== $hook) {
        return;
    }

    // Enqueue Prism CSS
    wp_enqueue_style(
        'prism-css',
        'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css',
        [],
        '1.29.0'
    );

    // Enqueue Prism JS
    wp_enqueue_script(
        'prism-js',
        'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js',
        [],
        '1.29.0',
        true
    );

    // Enqueue our admin script, with prism as a dependency
    wp_enqueue_script(
        'code-finder-admin-js',
        plugin_dir_url(__FILE__) . 'admin/assets/js/admin.js',
        ['jquery', 'prism-js'],
        '1.0.1', // Bump version
        true
    );

    // Pass data to JavaScript
    wp_localize_script('code-finder-admin-js', 'codeFinder', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('code_finder_ajax_nonce'),
    ]);
}
add_action('admin_enqueue_scripts', 'code_finder_enqueue_admin_scripts');

/**
 * AJAX handler for the code search.
 */
function code_finder_ajax_search() {
    // Check nonce for security
    check_ajax_referer('code_finder_ajax_nonce', 'nonce');

    // Sanitize user input
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
    $search_type = isset($_POST['search_type']) ? sanitize_key($_POST['search_type']) : 'any';
    $search_in = isset($_POST['search_in']) ? array_map('sanitize_key', $_POST['search_in']) : [];

    if (empty($search_term) || empty($search_in)) {
        wp_send_json_error(['message' => 'Search term and at least one location are required.']);
    }

    // Create a unique transient key
    $transient_key = 'cf_' . md5($search_term . $search_type . implode(',', $search_in));

    // Try to get cached results
    $cached_html = get_transient($transient_key);

    if (false !== $cached_html) {
        wp_send_json_success(['html' => $cached_html, 'cached' => true]);
        wp_die();
    }

    // Include necessary classes
    require_once plugin_dir_path(__FILE__) . 'includes/class-file-scanner.php';
    require_once plugin_dir_path(__FILE__) . 'includes/class-code-searcher.php';

    // Perform the search
    $scanner = new Code_Finder_File_Scanner();
    $files_to_search = $scanner->scan_files($search_in);

    $searcher = new Code_Finder_Code_Searcher();
    $search_results = $searcher->search($search_term, $files_to_search, $search_type);

    // Generate results HTML
    ob_start();
    if (!empty($search_results)) {
        echo '<p>Found ' . count($search_results) . ' result(s) for "<strong>' . esc_html($search_term) . '</strong>":</p>';
        echo '<ul class="search-results-list">';
        foreach ($search_results as $result) {
            $file_path = $result['file'];
            $extension = pathinfo($file_path, PATHINFO_EXTENSION);
            $lang_class = 'language-' . $extension;

            echo '<li>';
            echo '<strong>' . esc_html($file_path) . '</strong>';
            echo ' (Line ' . (int)$result['line_number'] . ')';
            echo '<pre><code class="' . esc_attr($lang_class) . '">' . esc_html($result['line_content']) . '</code></pre>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No results found for "<strong>' . esc_html($search_term) . '</strong>".</p>';
    }
    $html = ob_get_clean();

    // Store the results in the cache for 1 hour
    set_transient($transient_key, $html, HOUR_IN_SECONDS);

    wp_send_json_success(['html' => $html, 'cached' => false]);

    wp_die(); // This is required to terminate immediately and return a proper response
}
add_action('wp_ajax_code_finder_search', 'code_finder_ajax_search');
