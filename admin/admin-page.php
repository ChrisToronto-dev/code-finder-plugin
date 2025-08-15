<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <p>Search for code snippets across your WordPress installation using AJAX.</p>

    <form id="code-finder-form" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="search_term">Search Term</label>
                    </th>
                    <td>
                        <input type="text" id="search_term" name="search_term" class="regular-text" placeholder="e.g., my-class or myFunction" required>
                        <p class="description">Enter a CSS class name, JS function/variable name, or HTML tag.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="search_type">Search Type</label>
                    </th>
                    <td>
                        <select id="search_type" name="search_type">
                            <option value="any">Any Text</option>
                            <option value="css_class">CSS Class</option>
                            <option value="js_function">JS Function</option>
                            <option value="js_variable">JS Variable</option>
                            <option value="html_tag">HTML Tag</option>
                        </select>
                        <p class="description">Select the type of code to search for. This will make the search more precise.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Search In</th>
                    <td>
                        <fieldset>
                            <label><input type="checkbox" name="search_in[]" value="themes" checked> Themes</label><br>
                            <label><input type="checkbox" name="search_in[]" value="plugins" checked> Plugins</label><br>
                            <label><input type="checkbox" name="search_in[]" value="uploads"> Uploads</label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Search Code">
            <span class="spinner"></span>
        </p>
    </form>

    <hr/>

    <div id="search-results-container">
        <p>Please enter a search term and click "Search Code" to begin.</p>
    </div>
</div>
