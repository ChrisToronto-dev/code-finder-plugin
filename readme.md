=== Code Finder ===
Contributors: Gemini
Tags: search, code, css, javascript, developer, debug, find
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful and fast search tool for developers to find code snippets within WordPress themes, plugins, and upload directories.

== Description ==

Code Finder is an essential tool for WordPress developers and site administrators who need to locate specific pieces of code within their WordPress installation. Have you ever wondered which file contains a specific CSS class, a JavaScript function, or a particular HTML tag? Code Finder provides a simple and intuitive interface within the WordPress admin area to search through all your theme, plugin, and upload files.

**Key Features:**

*   **Precise Searches**: Search for plain text or use the built-in search types for more accurate results when looking for CSS classes, JavaScript functions/variables, or HTML tags.
*   **AJAX-Powered Interface**: The search is performed in the background without reloading the page, providing a fast and smooth user experience.
*   **Syntax Highlighting**: Search results are presented with syntax highlighting, making it easy to understand the context of the found code.
*   **Performance Optimized**: A built-in caching system stores recent search results, dramatically speeding up repeated searches and reducing server load.
*   **Targeted Search**: Choose to search within themes, plugins, uploads, or any combination.

This plugin is designed to be a lightweight and secure developer utility that lives in your admin dashboard.

== Installation ==

1.  Upload the `code-finder-plugin` folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Go to the "Code Finder" menu in your WordPress admin dashboard to start searching.

== Frequently Asked Questions ==

= Who is this plugin for? =

This plugin is designed for developers, designers, and site administrators who need to work with code and want a quick way to find where specific code snippets are located.

= Does this plugin slow down my website for visitors? =

No. The plugin's code only runs in the admin area when a logged-in administrator is actively using the search page. It has no performance impact on the public-facing side of your website.

= What kind of code can I search for? =

You can perform a general text search, or you can use the "Search Type" dropdown for a more precise search for:
*   CSS Classes
*   JavaScript Functions
*   JavaScript Variables
*   HTML Tags

= Are the search results cached? =

Yes. To ensure fast performance, identical searches are cached for one hour. This means if you search for the same term twice, the second search will be nearly instant.

== Screenshots ==

1.  The main search interface, showing the search term input, search type dropdown, and location checkboxes.
2.  An example of search results, showing the file path, line number, and the code snippet with syntax highlighting.

== Changelog ==

= 1.0.0 =
*   Initial release.
