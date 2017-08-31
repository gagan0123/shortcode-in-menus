<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Shortcode_In_Menus_Admin' ) && class_exists( 'Shortcode_In_Menus' ) ) {

	/**
	 * Handles Shortcode in Menus plugin interactions with WordPress.
	 * 
	 * @since 3.3
	 */
	class Shortcode_In_Menus_Admin extends Shortcode_In_Menus {

		/**
		 * Current instance of the class object.
		 * 
		 * @since 3.3
		 * @access protected
		 * @static
		 * 
		 * @var Shortcode_In_Menus_Admin
		 */
		protected static $instance = null;

		public function __construct() {

			//Calling parent class' constructor.
			parent::__construct();

			// setup the meta box
			add_action( 'admin_init', array( $this, 'setup_meta_box' ) );

			// enqueue custom js
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// add an ajax hack to save the html content
			add_action( 'wp_ajax_gs_sim_description_hack', array( $this, 'description_hack' ) );

			// hook to allow saving of shortcode in custom link metabox for legacy support
			add_action( 'wp_loaded', array( $this, 'security_check' ) );

			add_action( 'wp_ajax_add-menu-item', array( $this, 'ajax_add_menu_item' ), 0 );
		}

		/**
		 * Returns the current instance of the class Shortcode_In_Menus_Admin.
		 * 
		 * @since 3.3
		 * @access public
		 * @static
		 * 
		 * @return Shortcode_In_Menus_Admin Returns the current instance of the 
		 * 									class object.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Register our custom meta box.
		 * 
		 * @since 2.0
		 * @access public
		 * 
		 * @return void
		 */
		public function setup_meta_box() {
			add_meta_box( 'add-shortcode-section', __( 'Shortcode' ), array( $this, 'meta_box' ), 'nav-menus', 'side', 'default' );
		}

		/**
		 * Enqueue our custom JS.
		 * 
		 * @since 2.0
		 * @access public
		 * 
		 * @param string $hook The current screen.
		 * 
		 * @return void
		 */
		public function enqueue( $hook ) {

			// Don't enqueue if it isn't the menu editor.
			if ( 'nav-menus.php' != $hook ) {
				return;
			}
			
			wp_enqueue_script( 'gs-sim-admin', GS_SIM_URL . 'admin/js/shortcode-in-menus.js', array( 'nav-menu' ) );
		}

		/**
		 * An AJAX based workaround to save descriptions without using the 
		 * custom object type.
		 * 
		 * @since 2.0
		 * @access public
		 * 
		 * @return void
		 */
		public function description_hack() {
			// verify the nonce
			$nonce = $_POST[ 'description-nonce' ];
			if ( !wp_verify_nonce( $nonce, 'gs-sim-description-nonce' ) ) {
				die();
			}

			// get the menu item
			$item = $_POST[ 'menu-item' ];

			// save the description in a transient. This is what we'll use in setup_item()
			set_transient( 'gs_sim_description_hack_' . $item[ 'menu-item-object-id' ], $item[ 'menu-item-description' ] );

			// increment the object id, so it can be used by js
			$object_id = $this->new_object_id( $item[ 'menu-item-object-id' ] );

			echo $object_id;

			die();
		}

		/**
		 * Allows shortcodes into the custom link URL field.
		 * 
		 * @since 1.0
		 * 
		 * @return void
		 */
		public function security_check() {
			if ( current_user_can( 'activate_plugins' ) ) {
				//Conditionally adding the function for database context for 
				add_filter( 'clean_url', array( $this, 'save_shortcode' ), 99, 3 );
			}
		}

		/**
		 * Ajax handler for add menu item request.
		 * 
		 * @since 2.0
		 * 
		 * @return void
		 */
		public function ajax_add_menu_item() {

			check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

			if ( !current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

			// For performance reasons, we omit some object properties from the checklist.
			// The following is a hacky way to restore them when adding non-custom items.

			$menu_items_data = array();
			foreach ( (array) $_POST[ 'menu-item' ] as $menu_item_data ) {
				if (
				!empty( $menu_item_data[ 'menu-item-type' ] ) &&
				'custom' != $menu_item_data[ 'menu-item-type' ] &&
				'gs_sim' != $menu_item_data[ 'menu-item-type' ] &&
				!empty( $menu_item_data[ 'menu-item-object-id' ] )
				) {
					switch ( $menu_item_data[ 'menu-item-type' ] ) {
						case 'post_type' :
							$_object = get_post( $menu_item_data[ 'menu-item-object-id' ] );
							break;

						case 'taxonomy' :
							$_object = get_term( $menu_item_data[ 'menu-item-object-id' ], $menu_item_data[ 'menu-item-object' ] );
							break;
					}

					$_menu_items = array_map( 'wp_setup_nav_menu_item', array( $_object ) );
					$_menu_item	 = reset( $_menu_items );

					// Restore the missing menu item properties
					$menu_item_data[ 'menu-item-description' ] = $_menu_item->description;
				}

				$menu_items_data[] = $menu_item_data;
			}

			$item_ids = wp_save_nav_menu_items( 0, $menu_items_data );
			if ( is_wp_error( $item_ids ) ) {
				wp_die( 0 );
			}

			$menu_items = array();

			foreach ( (array) $item_ids as $menu_item_id ) {
				$menu_obj = get_post( $menu_item_id );
				if ( !empty( $menu_obj->ID ) ) {
					$menu_obj		 = wp_setup_nav_menu_item( $menu_obj );
					$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items
					$menu_items[]	 = $menu_obj;
				}
			}

			/** This filter is documented in wp-admin/includes/nav-menu.php */
			$walker_class_name = apply_filters( 'wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $_POST[ 'menu' ] );

			if ( !class_exists( $walker_class_name ) )
				wp_die( 0 );

			if ( !empty( $menu_items ) ) {
				$args = array(
					'after'			 => '',
					'before'		 => '',
					'link_after'	 => '',
					'link_before'	 => '',
					'walker'		 => new $walker_class_name,
				);
				echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
			}
			wp_die();
		}

		/**
		 * Method to allow saving of shortcodes in custom_link URL.
		 * 
		 * @since 1.0
		 * 
		 * @param string $url The processed URL for displaying/saving.
		 * @param string $orig_url The URL that was submitted, retreived.
		 * @param string $context Whether saving or displaying.
		 * 
		 * @return string String containing the shortcode.
		 */
		public function save_shortcode( $url, $orig_url, $context ) {

			if ( $context == 'db' && $this->has_shortcode( $orig_url ) ) {
				return $orig_url;
			}
			return $url;
		}

	}

}