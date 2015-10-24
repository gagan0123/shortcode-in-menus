=== Shortcodes in Menus ===
Contributors: gagan0123, saurabhshukla
Tags: Shortcode, Menus, Custom Link
Requires at least: 3.5
Tested up to: 4.3.1
Stable tag: 3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to add shortcodes in WordPress Navigation Menus

== Description ==

Allows you to add shortcodes in WordPress Navigation Menus so that you can generate links dynamically. Also allows you to add full fledged html sections to navigation menus.

> *Note*
> 
> This new version adds a *Shortcode* box on the Nav Menus screen. The earlier functionality using the *Links* box will be deprecated over the next few versions. Please switch to this new box, asap.

**Usage**

See the [screenshots](https://wordpress.org/plugins/shortcode-in-menus/).

Also, see a [great tutorial](https://wordpress.org/support/topic/how-does-it-work-24?replies=22#post-6160111) by Aurovrata Venet

**Special Thanks To**

* [Aurovrata Venet](https://wordpress.org/support/profile/aurovrata) for [this great tutorial](https://wordpress.org/support/topic/how-does-it-work-24?replies=22#post-6160111).
* [Lee Willis](https://wordpress.org/support/profile/leewillis77) for finding out and helping in resolving [this bug](https://wordpress.org/support/topic/causes-urls-to-be-amended-in-undesired-ways).

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
1. To test this, you can add a custom link with a ShortCode [gs_test_shortcode] as link, if it points to http://gagan.pro, plugin is working
1. If you want to use a ShortCode that outputs not just the url, but complete HTML sections, please make use of the title 'FULL HTML OUTPUT' for that link and it will output the complete HTML without breaking your site.

== Changelog ==

= 0.1 =
* Initial Plugin uploaded.

= 1.0 =
* Added prefix to function which was conflicting with another plugin

= 1.1 =
* Tested with WordPress 4.0

= 1.2 =
* Added ability to echo complete HTML output instead of just URL by using ShortCode

= 2.0 =
* Added new Shortcode box to Menu Editor
* Added html support.
* Deprecated Links box basis.
* Added screenshots
* Updated readme and instructions

= 2.1 =
* Bug fix for custom links with ShortCode like structure not being displayed in the nav menus.

= 3.0 =
* Removed the error trigger on the FULL HTML OUTPUT usage
* Added the feature to use shortcodes in titles of menu items as well(works with all types of menu items)
* Resolved the PHP Notice, popping up in the error log while adding new shortcodes

= 3.1 =
* Fixed [the bug](https://wordpress.org/support/topic/causes-urls-to-be-amended-in-undesired-ways) with clean_url filters as reported by [Lee Willis](https://wordpress.org/support/profile/leewillis77)
* Made the code translation ready.