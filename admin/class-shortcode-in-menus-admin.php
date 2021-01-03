<?php
/**
 * Handles admin side interactions of the plugin with WordPress.
 *
 * @package Shortcode_In_Menus
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Shortcode_In_Menus_Admin' ) && class_exists( 'Shortcode_In_Menus' ) ) {

	/**
	 * Handles admin side interactions of Shortcode in Menus plugin with WordPress.
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

		/**
		 * Admin side hooks, filters and registers everything appropriately.
		 *
		 * @since 3.3
		 * @access public
		 */
		public function __construct() {

			// Calling parent class' constructor.
			parent::__construct();

			// Setup the meta box.
			add_action( 'admin_init', array( $this, 'setup_meta_box' ) );

			// Enqueue custom JS.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// Add an ajax hack to save the html content.
			add_action( 'wp_ajax_gs_sim_description_hack', array( $this, 'description_hack' ) );

			// Hook to allow saving of shortcode in custom link metabox for legacy support.
			add_action( 'wp_loaded', array( $this, 'security_check' ) );

			// Hijack the ajax_add_menu_item function in order to save Shortcode menu item properly.
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
		 *                                  class object.
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
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
			add_meta_box( 'add-shortcode-section', __( 'Shortcode', 'shortcode-in-menus' ), array( $this, 'meta_box' ), 'nav-menus', 'side', 'default' );
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
			if ( 'nav-menus.php' !== $hook ) {
				return;
			}

			wp_enqueue_script( 'gs-sim-admin', SHORTCODE_IN_MENUS_URL . 'admin/js/shortcode-in-menus.min.js', array( 'nav-menu' ), SHORTCODE_IN_MENUS_RES, true );
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
			// Verify the nonce.
			$nonce = filter_input( INPUT_POST, 'description-nonce', FILTER_SANITIZE_STRING );
			if ( ! wp_verify_nonce( $nonce, 'gs-sim-description-nonce' ) ) {
				wp_die();
			}

			// Get the menu item. We need this unfiltered, so using FILTER_UNSAFE_RAW.
			// phpcs:ignore WordPressVIPMinimum.Security.PHPFilterFunctions.RestrictedFilter
			$item = filter_input( INPUT_POST, 'menu-item', FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY );

			// Save the description in a transient. This is what we'll use in setup_item().
			set_transient( 'gs_sim_description_hack_' . $item['menu-item-object-id'], $item['menu-item-description'] );

			// Increment the object id, so it can be used by JS.
			$object_id = $this->new_object_id( $item['menu-item-object-id'] );

			echo esc_js( $object_id );

			wp_die();
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
				// Conditionally adding the function for database context for.
				add_filter( 'clean_url', array( $this, 'save_shortcode' ), 99, 3 );
			}
		}

		/**
		 * Ajax handler for add menu item request.
		 *
		 * This method is hijacked from WordPress default ajax_add_menu_item
		 * so need to be updated accordingly.
		 *
		 * @since 2.0
		 *
		 * @return void
		 */
		public function ajax_add_menu_item() {

			check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

			if ( ! current_user_can( 'edit_theme_options' ) ) {
				wp_die( -1 );
			}

			require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

			// For performance reasons, we omit some object properties from the checklist.
			// The following is a hacky way to restore them when adding non-custom items.
			$menu_items_data = array();
			// Get the menu item. We need this unfiltered, so using FILTER_UNSAFE_RAW.
			// phpcs:ignore WordPressVIPMinimum.Security.PHPFilterFunctions.RestrictedFilter
			$menu_item = filter_input( INPUT_POST, 'menu-item', FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY );
			foreach ( $menu_item as $menu_item_data ) {
				if (
				! empty( $menu_item_data['menu-item-type'] ) &&
				'custom' !== $menu_item_data['menu-item-type'] &&
				'gs_sim' !== $menu_item_data['menu-item-type'] &&
				! empty( $menu_item_data['menu-item-object-id'] )
				) {
					switch ( $menu_item_data['menu-item-type'] ) {
						case 'post_type':
							$_object = get_post( $menu_item_data['menu-item-object-id'] );
							break;

						case 'taxonomy':
							$_object = get_term( $menu_item_data['menu-item-object-id'], $menu_item_data['menu-item-object'] );
							break;
					}

					$_menu_items = array_map( 'wp_setup_nav_menu_item', array( $_object ) );
					$_menu_item  = reset( $_menu_items );

					// Restore the missing menu item properties.
					$menu_item_data['menu-item-description'] = $_menu_item->description;
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
				if ( ! empty( $menu_obj->ID ) ) {
					$menu_obj        = wp_setup_nav_menu_item( $menu_obj );
					$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items.
					$menu_items[]    = $menu_obj;
				}
			}

			$menu = filter_input( INPUT_POST, 'menu', FILTER_SANITIZE_NUMBER_INT );
			/** This filter is documented in wp-admin/includes/nav-menu.php */
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$walker_class_name = apply_filters( 'wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $menu );

			if ( ! class_exists( $walker_class_name ) ) {
				wp_die( 0 );
			}

			if ( ! empty( $menu_items ) ) {
				$args = array(
					'after'       => '',
					'before'      => '',
					'link_after'  => '',
					'link_before' => '',
					'walker'      => new $walker_class_name(),
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

			if ( 'db' === $context && $this->has_shortcode( $orig_url ) ) {
				return $orig_url;
			}
			return $url;
		}

		/**
		 * Gets a new object ID, given the current one
		 *
		 * @since 2.0
		 * @access public
		 *
		 * @param int $last_object_id The current/last object id.
		 *
		 * @return int Returns new object ID.
		 */
		public function new_object_id( $last_object_id ) {

			// make sure it's an integer.
			$object_id = (int) $last_object_id;

			// increment it.
			$object_id ++;

			// if object_id was 0 to start off with, make it 1.
			$object_id = ( $object_id < 1 ) ? 1 : $object_id;

			// save into the options table.
			update_option( 'gs_sim_last_object_id', $object_id );

			return $object_id;
		}

		/**
		 * Display our custom meta box.
		 *
		 * @since 2.0
		 * @access public
		 *
		 * @global int $_nav_menu_placeholder        A placeholder index for the menu item.
		 * @global int|string $nav_menu_selected_id  (id, name or slug) of the currently-selected menu.
		 *
		 * @return void
		 */
		public function meta_box() {
			global $_nav_menu_placeholder, $nav_menu_selected_id;

			$nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

			$last_object_id = get_option( 'gs_sim_last_object_id', 0 );
			$object_id      = $this->new_object_id( $last_object_id );
			?>
			<div class="gs-sim-div" id="gs-sim-div">
				<input type="hidden" class="menu-item-db-id" name="menu-item[<?php echo esc_attr( $nav_menu_placeholder ); ?>][menu-item-db-id]" value="0" />
				<input type="hidden" class="menu-item-object-id" name="menu-item[<?php echo esc_attr( $nav_menu_placeholder ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $object_id ); ?>" />
				<input type="hidden" class="menu-item-object" name="menu-item[<?php echo esc_attr( $nav_menu_placeholder ); ?>][menu-item-object]" value="gs_sim" />
				<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $nav_menu_placeholder ); ?>][menu-item-type]" value="gs_sim" />
				<input type="hidden" id="gs-sim-description-nonce" value="<?php echo esc_attr( wp_create_nonce( 'gs-sim-description-nonce' ) ); ?>" />
				<p id="menu-item-title-wrap">
					<label for="gs-sim-title"><?php esc_html_e( 'Title', 'shortcode-in-menus' ); ?></label>
					<input id="gs-sim-title" name="menu-item[<?php echo esc_attr( $nav_menu_placeholder ); ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox" title="<?php esc_attr_e( 'Title', 'shortcode-in-menus' ); ?>" style="width:100%" />
				</p>

				<p id="menu-item-html-wrap">
					<textarea style="width:100%;" rows="9" id="gs-sim-html" name="menu-item[<?php echo esc_attr( $nav_menu_placeholder ); ?>][menu-item-description]" class="code menu-item-textbox" title="<?php esc_attr_e( 'Text/HTML/shortcode here!', 'shortcode-in-menus' ); ?>"></textarea>
				</p>

				<p class="button-controls">
					<span class="add-to-menu">
						<input type="submit" <?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'shortcode-in-menus' ); ?>" name="add-gs-sim-menu-item" id="submit-gs-sim" />
						<span class="spinner"></span>
					</span>
				</p>

			</div>
			<?php
		}

	}

}
