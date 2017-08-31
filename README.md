<img src='https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/icon-128x128.png' align='right' />

# Shortcodes in Menus #
**Contributors:** [gagan0123](https://profiles.wordpress.org/gagan0123), [saurabhshukla](https://profiles.wordpress.org/saurabhshukla)  
**Tags:** Shortcode, Menus, Custom Link  
**Requires at least:** 3.5  
**Tested up to:** 4.7.5  
**Stable tag:** 3.2  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Allows you to add shortcodes in WordPress Navigation Menus

## Description ##

Allows you to add shortcodes in WordPress Navigation Menus so that you can generate links dynamically. Also allows you to add full fledged html sections to navigation menus.


**Usage**

See the [screenshots](https://wordpress.org/plugins/shortcode-in-menus/#screenshots).

Also, see a [great tutorial](https://wordpress.org/support/topic/how-does-it-work-24/page/2/#post-4987738) by Aurovrata Venet

**Special Thanks To**

* [Aurovrata Venet](https://wordpress.org/support/profile/aurovrata) for [this great tutorial](https://wordpress.org/support/topic/how-does-it-work-24/page/2/#post-4987738).
* [Lee Willis](https://wordpress.org/support/profile/leewillis77) for finding out and helping in resolving [this bug](https://wordpress.org/support/topic/causes-urls-to-be-amended-in-undesired-ways).

## Screenshots ##

### 1. Check the screen options, if you don't see the *Shortcode* box. ###
![Check the screen options, if you don't see the *Shortcode* box.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-1.png)

### 2. Check the Shortcode option to see the new Shortcode box. ###
![Check the Shortcode option to see the new Shortcode box.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-2.png)

### 3. Add your shortcode/HTML to the text area (not a link, in the screenshot). Optionally, add a title. ###
![Add your shortcode/HTML to the text area (not a link, in the screenshot). Optionally, add a title.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-3.png)

### 4. The menu item is saved. ###
![The menu item is saved.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-4.png)

### 5. The html is displayed. ###
![The html is displayed.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-5.png)

### 6. Old Method: In the *Links* box, add your shortcode in the URL field. ###
![Old Method: In the *Links* box, add your shortcode in the URL field.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-6.png)

### 7. Old Method: If you want to use a shortcode that outputs not just the URL, but complete HTML sections, write *FULL HTML OUTPUT* in the *Link Text* option for that link and it will output the complete HTML without breaking your site. ###
![Old Method: If you want to use a shortcode that outputs not just the URL, but complete HTML sections, write *FULL HTML OUTPUT* in the *Link Text* option for that link and it will output the complete HTML without breaking your site.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-7.png)

### 8. Old Method: The menu item is saved. ###
![Old Method: The menu item is saved.](https://github.com/gagan0123/shortcode-in-menus/raw/master/assets/screenshot-8.png)


## Installation ##

1. Add the plugin's folder in the WordPress' plugin directory.
1. Activate the plugin.
1. You can now add ShortCodes in the custom links of the menus.
1. To test this, you can add a custom link with a ShortCode [gs_test_shortcode] as link, if it points to http://gagan.pro, plugin is working
1. If you want to use a ShortCode that outputs not just the url, but complete HTML sections, please make use of the title 'FULL HTML OUTPUT' for that link and it will output the complete HTML without breaking your site.

## Changelog ##

### 0.1 ###
* Initial Plugin uploaded.

### 1.0 ###
* Added prefix to function which was conflicting with another plugin

### 1.1 ###
* Tested with WordPress 4.0

### 1.2 ###
* Added ability to echo complete HTML output instead of just URL by using ShortCode

### 2.0 ###
* Added new Shortcode box to Menu Editor
* Added html support.
* Deprecated Links box basis.
* Added screenshots
* Updated readme and instructions

### 2.1 ###
* Bug fix for custom links with ShortCode like structure not being displayed in the nav menus.

### 3.0 ###
* Removed the error trigger on the FULL HTML OUTPUT usage
* Added the feature to use shortcodes in titles of menu items as well(works with all types of menu items)
* Resolved the PHP Notice, popping up in the error log while adding new shortcodes

### 3.1 ###
* Fixed [the bug](https://wordpress.org/support/topic/causes-urls-to-be-amended-in-undesired-ways) with clean_url filters as reported by [Lee Willis](https://wordpress.org/support/profile/leewillis77)
* Made the code translation ready.

### 3.2 ###
* Code Refactoring
* Changed tested upto
* Corrected links in description
