=== Shortcode in Menus ===
Contributors: gagan0123, saurabhshukla
Donate Link: https://PayPal.me/gagan0123
Tags: Shortcode, Menus, Custom Link
Requires at least: 3.6
Requires PHP: 5.6
Tested up to: 5.6
Stable tag: 3.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to add shortcodes in WordPress Navigation Menus.

== Description ==

Allows you to add shortcodes in WordPress Navigation Menus so that you can generate links dynamically. Also allows you to add full fledged HTML sections to navigation menus.


**Usage**

See the [screenshots](#screenshots).

Also, see a [great tutorial](https://wordpress.org/support/topic/how-does-it-work-24/page/2/#post-4987738) by Aurovrata Venet

**Special Thanks To**

* [Aurovrata Venet](https://wordpress.org/support/profile/aurovrata) for [this great tutorial](https://wordpress.org/support/topic/how-does-it-work-24/page/2/#post-4987738).
* [Lee Willis](https://wordpress.org/support/profile/leewillis77) for finding out and helping in resolving [this bug](https://wordpress.org/support/topic/causes-urls-to-be-amended-in-undesired-ways).
* [Dennis Hunink](https://wordpress.org/support/users/dhunink/) for reporting [this bug](https://wordpress.org/support/?p=10325305).
* [@hbwarper](https://wordpress.org/support/users/hbwarper/) for providing a patch to Dennis' issue.

== Screenshots ==

1. Check the screen options, if you don't see the *Shortcode* box.
1. Check the Shortcode option to see the new Shortcode box.
1. Add your shortcode/HTML to the text area (not a link, in the screenshot). Optionally, add a title.
1. The menu item is saved.
1. The html is displayed.
1. Old Method: In the *Links* box, add your shortcode in the URL field.
1. Old Method: If you want to use a shortcode that outputs not just the URL, but complete HTML sections, write *FULL HTML OUTPUT* in the *Link Text* option for that link and it will output the complete HTML without breaking your site.
1. Old Method: The menu item is saved.

== Installation ==

1. Add the plugin's folder in the WordPress' plugin directory.
1. Activate the plugin.
1. You can now add ShortCodes in the custom links of the menus.
1. To test this, you can add a custom link with a ShortCode [gs_test_shortcode] as link, if it points to https://wordpress.org, plugin is working
1. If you want to use a ShortCode that outputs not just the url, but complete HTML sections, please make use of the title 'FULL HTML OUTPUT' for that link and it will output the complete HTML without breaking your site.

== Frequently Asked Questions ==

= How to enter shortcode =

You can add enter shortcodes in the "Custom Link" menu item, or you can use "Shortcode" menu shortcodes in menus.

Screencast for using WordPress' default "Custom Links" menu item:

https://www.youtube.com/watch?v=NIu-e9XjEXM

Screencast for using "Shortcode" menu item:

https://www.youtube.com/watch?v=a7oZq1fgDO4

= How to view/edit "Shortcode" menu item =

You will need to enable displaying of "Description" in order to view/edit "Shortcode" menu item.

Here's a screencast for the same:

https://www.youtube.com/watch?v=IzaUf5KHehg

== Changelog ==

= 3.5.1 =
* Change plugin constants to follow naming conventions as recommended by WordPress Guidelines.
* Strict input filters for admin pages, for user input values.
* Adhere to more strict PHPCS ruleset.

= 3.5 =
* Resolved some WPCS compatibility issues.
* Added resource version for static resources for busting cache in new releases.
* Added text domain to localisable text strings.
* Loading the custom JS in footer instead of header for performance benefit.
* Handle PHP notice in custom cases when start_el is not passed with $item object.

= 3.4 =
* Escaping of output within a lot of functions.
* Made the code WordPress PHPCS Compatible.
* Patch to make it work with Max Mega Menu plugin.

= 3.3 =
* Fixed a compatibility issue with Twenty Fifteen theme.
* Minified JS.
* Conditional loading of admin class for performance improvement.
* Some more code refactoring.
* Testing with WordPress 4.8.1
* Changed minimum required WordPress version from 3.5 to 3.6

= 3.2 =
* Code Refactoring.
* Changed tested upto.
* Corrected links in description.

= 3.1 =
* Fixed [the bug](https://wordpress.org/support/topic/causes-urls-to-be-amended-in-undesired-ways) with clean_url filters as reported by [Lee Willis](https://wordpress.org/support/profile/leewillis77)
* Made the code translation ready.

= 3.0 =
* Removed the error trigger on the FULL HTML OUTPUT usage.
* Added the feature to use shortcodes in titles of menu items as well(works with all types of menu items).
* Resolved the PHP Notice, popping up in the error log while adding new shortcodes.

= 2.1 =
* Bug fix for custom links with ShortCode like structure not being displayed in the nav menus.

= 2.0 =
* Added new Shortcode box to Menu Editor.
* Added html support.
* Deprecated Links box basis.
* Added screenshots.
* Updated readme and instructions.

= 1.2 =
* Added ability to echo complete HTML output instead of just URL by using ShortCode.

= 1.1 =
* Tested with WordPress 4.0

= 1.0 =
* Added prefix to function which was conflicting with another plugin.

= 0.1 =
* Initial Plugin uploaded.