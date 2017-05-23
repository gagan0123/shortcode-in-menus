<?php

/*
  Plugin Name: Shortcodes in Menus
  Description: Allows you to add shortcodes in WordPress Navigation Menus
  Plugin URI: http://wordpress.org/plugins/shortcode-in-menus/
  Version: 3.2
  Author: <a href="https://gagan0123.com">Gagan Deep Singh</a> and <a href="http://hookrefineandtinker.com">Saurabh Shukla</a>
  Text Domain: shortcode-in-menus
 */

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

//Lets define some constants
define( 'GS_SIM_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'GS_SIM_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );

/**
 * The core plugin class
 */
require_once GS_SIM_PATH . 'includes/class-shortcode-in-menus.php';
