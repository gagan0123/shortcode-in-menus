<?php

/*
  Plugin Name: Shortcodes in Menus
  Description: Allows you to add shortcodes in WordPress Navigation Menus
  Plugin URI: http://wordpress.org/plugins/shortcode-in-menus/
  Version: 1.2
  Author URI: http://gagan.pro
  Author: Gagan Deep Singh
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Allows shortcode to be saved in database
 */

function gs_sim_allow_saving_shortcode_custom_links($url, $orig_url, $context) {
    if ($context == 'db') {
        return $orig_url;
    }
    return $url;
}

function gs_sim_security_check() {
    if (current_user_can('activate_plugins')) {
        //Conditionally adding the function for database context for 
        add_filter('clean_url', 'gs_sim_allow_saving_shortcode_custom_links', 99, 3);
    }
}

add_action('wp_loaded', 'gs_sim_security_check');

/**
 * Allows shortcode to be processed and displayed
 */
function gs_sim_allow_display_shortcode_custom_links($url, $orig_url, $context) {
    if ($context == 'display') {
        return do_shortcode($orig_url);
    }
    return $url;
}

add_filter('clean_url', 'gs_sim_allow_display_shortcode_custom_links', 1, 3);

/**
 * Adding a test shortcode for testing this plugin
 * */
add_shortcode('gs_test_shortcode', 'gs_sim_test_shortcode');

/**
 * Returns "Hello World" for testing the shortcode
 */
function gs_sim_test_shortcode($data) {
    return "http://gagan.pro";
}

if (!function_exists('gs_sim_menu_item')) {

    /**
     * Allows adding of full HTML supported shortcodes instead of just URL's
     */
    function gs_sim_menu_item($item_output, $item) {
        if ($item->post_title == 'FULL HTML OUTPUT') {
            $item_output = do_shortcode($item->url);
        }
        return $item_output;
    }

}
add_filter('walker_nav_menu_start_el', 'gs_sim_menu_item', 10, 2);
