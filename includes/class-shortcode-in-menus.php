<?php
/**
 * Main class of the plugin interacting with WordPress.
 *
 * @package Shortcode_In_Menus
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Shortcode_In_Menus' ) ) {

	/**
	 * Handles Shortcode in Menus plugin interactions with WordPress.
	 *
	 * @since 3.2
	 */
	class Shortcode_In_Menus {

		/**
		 * Current instance of the class object.
		 *
		 * @since 3.2
		 * @access protected
		 * @static
		 *
		 * @var Shortcode_In_Menus
		 */
		protected static $instance = null;

		/**
		 * Returns the current instance of the class Shortcode_In_Menus.
		 *
		 * @since 3.2
		 * @access public
		 * @static
		 *
		 * @return Shortcode_In_Menus Returns the current instance of the class object.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Hooks, filters and registers everything appropriately.
		 *
		 * @since 3.2
		 * @access public
		 */
		public function __construct() {

			// register a test shortcode for testing.
			add_shortcode( 'gs_test_shortcode', array( $this, 'shortcode' ) );

			// filter the menu item output on frontend.
			add_filter( 'walker_nav_menu_start_el', array( $this, 'start_el' ), 20, 2 );

			// Making it work with Max Mega Menu Plugin.
			add_filter( 'megamenu_walker_nav_menu_start_el', array( $this, 'start_el' ), 20, 2 );

			// filter the output when shortcode is saved using custom links, for legacy support.
			add_filter( 'clean_url', array( $this, 'display_shortcode' ), 1, 3 );

			// filter the menu item before display in admin and in frontend.
			add_filter( 'wp_setup_nav_menu_item', array( $this, 'setup_item' ), 10, 1 );
		}

		/**
		 * Test shortcode. Output's WordPress.org URL.
		 *
		 * @since 1.2
		 * @access public
		 *
		 * @return string Returns WordPress.org URL.
		 */
		public function shortcode() {
			return __( 'https://wordpress.org', 'shortcode-in-menus' );
		}

		/**
		 * Check if the passed content has any shortcode. Inspired from the
		 * core's has_shortcode.
		 *
		 * @since 2.0
		 * @access public
		 *
		 * @param string $content The content to check for shortcode.
		 *
		 * @return boolean Returns true if the $content has shortcode, false otherwise.
		 */
		public function has_shortcode( $content ) {

			if ( false !== strpos( $content, '[' ) ) {

				preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );

				if ( ! empty( $matches ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Modifies the menu item display on frontend.
		 *
		 * @since 2.0
		 *
		 * @param string $item_output The original html.
		 * @param object $item  The menu item being displayed.
		 *
		 * @return string Modified menu item to display.
		 */
		public function start_el( $item_output, $item ) {
			// Rare case when $item is not an object, usually with custom themes.
			if ( ! is_object( $item ) || ! isset( $item->object ) ) {
				return $item_output;
			}

			// if it isn't our custom object.
			if ( 'gs_sim' !== $item->object ) {

				// check the legacy hack.
				if ( isset( $item->post_title ) && 'FULL HTML OUTPUT' === $item->post_title ) {

					// then just process as we used to.
					$item_output = do_shortcode( $item->url );
				} else {
					$item_output = do_shortcode( $item_output );
				}

				// if it is our object.
			} elseif ( isset( $item->description ) ) {
				// just process it.
				$item_output = do_shortcode( $item->description );
			}

			return $item_output;
		}

		/**
		 * Allows shortcode to be processed and displayed.
		 *
		 * @since 1.0
		 *
		 * @param string $url       The processed URL for displaying/saving.
		 * @param string $orig_url  The URL that was submitted, retrieved.
		 * @param string $context   Whether saving or displaying.
		 *
		 * @return string Output string after shortcode has been executed.
		 */
		public function display_shortcode( $url, $orig_url, $context ) {
			if ( 'display' === $context && $this->has_shortcode( $orig_url ) ) {
				return do_shortcode( $orig_url );
			}
			return $url;
		}

		/**
		 * Modify the menu item before display on Menu editor and in frontend.
		 *
		 * @since 2.0
		 * @access public
		 *
		 * @param object $item The menu item.
		 *
		 * @return object Modified menu item object.
		 */
		public function setup_item( $item ) {
			if ( ! is_object( $item ) ) {
				return $item;
			}

			// only if it is our object.
			if ( 'gs_sim' === $item->object ) {

				// setup our label.
				$item->type_label = __( 'Shortcode', 'shortcode-in-menus' );

				if ( ! empty( $item->post_content ) ) {
					$item->description = $item->post_content;
				} else {

					// set up the description from the transient.
					$item->description = get_transient( 'gs_sim_description_hack_' . $item->object_id );

					// discard the transient.
					delete_transient( 'gs_sim_description_hack_' . $item->object_id );
				}
			}
			return $item;
		}

	}

}
