# Shortcode in Menus

**Contributors:** [gagan0123](https://profiles.wordpress.org/gagan0123), [saurabhshukla](https://profiles.wordpress.org/saurabhshukla)
**Donate Link:** [https://PayPal.me/gagan0123](https://PayPal.me/gagan0123)
**Tags:** Shortcode, Menus, Custom Link
**Requires at least:** 3.6
**Requires PHP:** 5.6
**Tested up to:** 5.6
**Stable tag:** 3.5.1
**License:** GPLv2 or later
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

Allows you to add shortcodes in WordPress Navigation Menus.

## 1. Project Overview

- **Name**: Shortcode in Menus
- **Description**: This WordPress plugin enables users to embed shortcodes and custom HTML directly into WordPress Navigation Menus. It allows for dynamic link generation and the insertion of full HTML sections within the menu structure.
- **Status**: Production (Version 3.5.1)

## 2. Documentation Correction

*Note: This README replaces the previous documentation generated from `readme.txt`. The original `readme.txt` is geared towards the WordPress Plugin Directory.*

- **Updates**:
    - clarified the distinction between the "Modern" method (using the specific Shortcode metabox) and the "Legacy" method (using Custom Links).
    - Added explicit developer setup instructions using Grunt.
    - Verified that no unit tests are currently active (sample test is excluded).

## 3. Tech Stack

- **Languages**: PHP (>= 5.6), JavaScript (jQuery), HTML, CSS.
- **Framework**: WordPress (>= 3.6).
- **Build Tools**: Grunt (npm) - used for minification, i18n pot generation, and readme conversion.
- **Testing**: PHPUnit (Scaffolded, but currently no active tests).

## 4. Project Structure

```
.
├── admin/                  # Admin-side logic and assets
│   ├── class-shortcode-in-menus-admin.php  # Admin UI and AJAX handlers
│   └── js/                 # Admin JavaScript (source and minified)
├── assets/                 # Static assets (images, icons)
├── includes/               # Core plugin logic
│   └── class-shortcode-in-menus.php        # Shortcode processing and rendering
├── languages/              # Translation files (.pot)
├── tests/                  # PHPUnit test scaffolding
├── Gruntfile.js            # Build configuration
├── package.json            # Node dependencies
├── readme.txt              # WordPress Plugin Directory readme
└── shortcode-in-menus.php  # Main plugin entry point
```

**Key Files:**
- `shortcode-in-menus.php`: Initializes the plugin, defines constants, and loads dependencies.
- `includes/class-shortcode-in-menus.php`: Handles the frontend rendering, hooking into `walker_nav_menu_start_el` to process shortcodes.
- `admin/class-shortcode-in-menus-admin.php`: Adds the "Shortcode" metabox to the Menu Editor and handles AJAX requests for saving menu item data.

## 5. Installation & Setup (Verified)

### User Installation
1. Upload the `shortcode-in-menus` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

### Developer Installation
1. Clone the repository.
2. Install Node.js dependencies:
   ```bash
   npm install
   ```
3. Run Grunt to watch for changes or build assets:
   ```bash
   grunt
   ```
   *The default Grunt task watches for changes in JS and `readme.txt`.*

### Environment Variables
This project relies on standard WordPress configuration. No additional environment variables are required for basic operation.

## 6. Features & Usage

### 1. Shortcode Menu Item (Recommended)
This is the modern way to add shortcodes or HTML blocks.
1. Go to **Appearance > Menus**.
2. If you don't see the "Shortcode" box on the left, click **Screen Options** at the top right and check **Shortcode**.
3. In the "Shortcode" box, add a **Title** (optional) and your content (Shortcode or HTML) in the large text area.
4. Click **Add to Menu**.
5. The content will be rendered inside the menu item.

### 2. Shortcodes in Custom Links (Legacy)
You can still use standard "Custom Link" items:
1. Add a **Custom Link** to your menu.
2. Enter the shortcode in the **URL** field (e.g., `[my_shortcode]`).
3. The shortcode output will replace the URL.

### 3. Full HTML Output (Legacy)
To output raw HTML using a Custom Link:
1. Add a **Custom Link**.
2. Set the **Navigation Label** to `FULL HTML OUTPUT`.
3. Enter your HTML or Shortcode in the **URL** field.
    *   *Note: The Modern "Shortcode Menu Item" method is preferred for this use case as it provides a larger text area.*

### 4. Shortcodes in Menu Titles
You can use shortcodes in the **Navigation Label** of any menu item.

### 5. Test Shortcode
The plugin includes a test shortcode for verification:
- `[gs_test_shortcode]` -> Outputs `https://wordpress.org`

## Changelog

### 3.5.1
* Change plugin constants to follow naming conventions as recommended by WordPress Guidelines.
* Strict input filters for admin pages, for user input values.
* Adhere to more strict PHPCS ruleset.

### 3.5
* Resolved some WPCS compatibility issues.
* Added resource version for static resources for busting cache in new releases.
* Added text domain to localisable text strings.
* Loading the custom JS in footer instead of header for performance benefit.
* Handle PHP notice in custom cases when start_el is not passed with $item object.

### 3.4
* Escaping of output within a lot of functions.
* Made the code WordPress PHPCS Compatible.
* Patch to make it work with Max Mega Menu plugin.

### 3.3
* Fixed a compatibility issue with Twenty Fifteen theme.
* Minified JS.
* Conditional loading of admin class for performance improvement.
* Some more code refactoring.
* Testing with WordPress 4.8.1
* Changed minimum required WordPress version from 3.5 to 3.6

### 3.2
* Code Refactoring.
* Changed tested upto.
* Corrected links in description.

### 3.1
* Fixed [the bug](https://wordpress.org/support/topic/causes-urls-to-be-amended-in-undesired-ways) with clean_url filters as reported by [Lee Willis](https://wordpress.org/support/profile/leewillis77)
* Made the code translation ready.

### 3.0
* Removed the error trigger on the FULL HTML OUTPUT usage.
* Added the feature to use shortcodes in titles of menu items as well(works with all types of menu items).
* Resolved the PHP Notice, popping up in the error log while adding new shortcodes.

### 2.1
* Bug fix for custom links with ShortCode like structure not being displayed in the nav menus.

### 2.0
* Added new Shortcode box to Menu Editor.
* Added html support.
* Deprecated Links box basis.
* Added screenshots.
* Updated readme and instructions.

### 1.2
* Added ability to echo complete HTML output instead of just URL by using ShortCode.

### 1.1
* Tested with WordPress 4.0

### 1.0
* Added prefix to function which was conflicting with another plugin.

### 0.1
* Initial Plugin uploaded.
