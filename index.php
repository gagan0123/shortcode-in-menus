<?php
/*
  Plugin Name: Shortcodes in Menus
  Description: Allows you to add shortcodes in WordPress Navigation Menus
  Plugin URI: http://wordpress.org/plugins/shortcode-in-menus/
  Version: 3.1
  Author: <a href="http://gagan.pro">Gagan Deep Singh</a> and <a href="http://hookrefineandtinker.com">Saurabh Shukla</a>
  Text Domain: shortcode-in-menus
 */

if ( !defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( !class_exists( 'gsShortCodeInMenu' ) ) {

	class gsShortCodeInMenu {

		/**
		 * Hooks, filters and registers everything appropriately
		 */
		public function init() {

			// register a test shortcode for testing
			add_shortcode( 'gs_test_shortcode', array( $this, 'shortcode' ) );

			// setup the meta box
			add_action( 'admin_init', array( $this, 'setup_meta_box' ) );

			// filter the menu item output on frontend
			add_filter( 'walker_nav_menu_start_el', array( $this, 'start_el' ), 10, 2 );

			// filter the menu item before display in admin
			add_filter( 'wp_setup_nav_menu_item', array( $this, 'setup_item' ), 10, 1 );

			// enqueue custom js
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// add an ajax hack to save the html content
			add_action( 'wp_ajax_gs_sim_description_hack', array( $this, 'description_hack' ) );

			// hook to allow saving of shortcode in custom link metabox for legacy support
			add_action( 'wp_loaded', array( $this, 'security_check' ) );

			// filter the output when shortcode is saved using custom links, for legacy support
			add_filter( 'clean_url', array( $this, 'display_shortcode' ), 1, 3 );

			add_action( 'wp_ajax_add-menu-item', array( $this, 'ajax_add_menu_item' ), 0 );
		}

		/**
		 * Test shortcode. Output's the developer's url
		 * 
		 * @return string
		 */
		public function shortcode() {
			return "http://gagan.pro";
		}

		/**
		 * Gets a new object id,given the current one
		 * 
		 * @param int $last_object_id The current/last object id
		 * @return int
		 */
		public function new_object_id( $last_object_id ) {

			// make sure it's an integer
			$object_id = (int) $last_object_id;

			// increment it
			$object_id++;

			// if object_id was 0 to start off with, make it 1
			$object_id = ($object_id < 1) ? 1 : $object_id;

			// save into the options table
			update_option( 'gs_sim_last_object_id', $object_id );

			return $object_id;
		}

		/**
		 * Register our custom meta box
		 */
		public function setup_meta_box() {
			add_meta_box( 'add-shortcode-section', __( 'Shortcode' ), array( $this, 'meta_box' ), 'nav-menus', 'side', 'default' );
		}

		/**
		 * Display our custom meta box
		 * @global int $_nav_menu_placeholder   A placeholder index for the menu item
		 * @global int|string $nav_menu_selected_id    (id, name or slug) of the currently-selected menu
		 */
		public function meta_box() {
			global $_nav_menu_placeholder, $nav_menu_selected_id;

			$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

			$last_object_id	 = get_option( 'gs_sim_last_object_id', 0 );
			$object_id		 = $this->new_object_id( $last_object_id );
			?>
			<div class="gs-sim-div" id="gs-sim-div">
				<input type="hidden" class="menu-item-db-id" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-db-id]" value="0" />
				<input type="hidden" class="menu-item-object-id" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="<?php echo $object_id; ?>" />
				<input type="hidden" class="menu-item-object" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object]" value="gs_sim" />
				<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="gs_sim" />
				<input type="hidden" id="gs-sim-description-nonce" value="<?php echo wp_create_nonce( 'gs-sim-description-nonce' ) ?>" />
				<p id="menu-item-title-wrap">
					<label for="gs-sim-title"><?php _e( 'Title' ); ?></label>
					<input id="gs-sim-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox" title="<?php esc_attr_e( 'Title' ); ?>" style="width:100%" />    
				</p>

				<p id="menu-item-html-wrap">
					<textarea style="width:100%;" rows="9" id="gs-sim-html" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-description]" class="code menu-item-textbox" title="<?php esc_attr_e( 'Text/HTML/shortcode here!' , 'shortcode-in-menus'); ?>"></textarea>
				</p>

				<p class="button-controls">
					<span class="add-to-menu">
						<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-gs-sim-menu-item" id="submit-gs-sim" />
						<span class="spinner"></span>
					</span>
				</p>

			</div>
			<?php
		}

		/**
		 * Check if the passed content has any shortcode. Inspired from the core's has_shortcode
		 * 
		 * @param string $content The content to check for shortcode
		 * @return boolean
		 * @author Saurabh Shukla
		 */
		function has_shortcode( $content ) {

			if ( false !== strpos( $content, '[' ) ) {

				preg_match_all( '/' . get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER );

				if ( !empty( $matches ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Modifies the menu item display on frontend
		 * 
		 * @param string $item_output The original html.
		 * @param object $item  The menu item being displayed.
		 * @return object
		 */
		public function start_el( $item_output, $item ) {
			// if it isn't our custom object
			if ( $item->object != 'gs_sim' ) {

				// check the legacy hack
				if ( $item->post_title == 'FULL HTML OUTPUT' ) {

					// then just process as we used to
					$item_output = do_shortcode( $item->url );
				} else {
					$item_output = do_shortcode( $item_output );
				}

				// if it is our object
			} else {
				// just process it
				$item_output = do_shortcode( $item->description );
			}

			return $item_output;
		}

		/**
		 * Modify the menu item before display on Menu editor
		 * 
		 * @param object $item The menu item
		 * @return object
		 */
		public function setup_item( $item ) {
			if ( !is_object( $item ) )
				return $item;

			// only if it is our object
			if ( $item->object == 'gs_sim' ) {

				// setup our label
				$item->type_label = __( 'Shortcode' );

				if ( $item->post_content != '' ) {
					$item->description = $item->post_content;
				} else {

					// set up the description from the transient
					$item->description = get_transient( 'gs_sim_description_hack_' . $item->object_id );

					// discard the transient
					delete_transient( 'gs_sim_description_hack_' . $item->object_id );
				}
			}
			return $item;
		}

		/**
		 * Enqueue our custom js
		 * 
		 * @param string $hook The current screen
		 * @return null
		 */
		public function enqueue( $hook ) {

			// don't enqueue if it isn't the menu editor
			if ( 'nav-menus.php' != $hook )
				return;

			// otherwise enqueue with nav-menu.js as a dependency so that our script is loaded after it
			wp_enqueue_script(
			'gs-sim-admin', plugins_url( '/js/admin.js', __FILE__ ), array( 'nav-menu' )
			);
		}

		/**
		 * An ajax based workaround to save descriptions without using the custom object type
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
		 * Legacy method to allow saving of shortcodes in custom_link url
		 * 
		 * @deprecated since 2.0
		 * 
		 * @param string $url The processed url for displaying/saving
		 * @param string $orig_url The url that was submitted, retreived
		 * @param string $context Whether saving or displaying
		 * @return string
		 */
		public function save_shortcode( $url, $orig_url, $context ) {

			if ( $context == 'db' && $this->has_shortcode( $orig_url) ) {
				return $orig_url;
			}
			return $url;
		}

		/**
		 * Allows shortcodes into the custom link url field
		 * 
		 * @deprecated since 2.0
		 */
		public function security_check() {
			if ( current_user_can( 'activate_plugins' ) ) {
				//Conditionally adding the function for database context for 
				add_filter( 'clean_url', array( $this, 'save_shortcode' ), 99, 3 );
			}
		}

		/**
		 * Allows shortcode to be processed and displayed
		 * 
		 * @deprecated since 2.0
		 * 
		 * @param string $url The processed url for displaying/saving
		 * @param string $orig_url The url that was submitted, retreived
		 * @param string $context Whether saving or displaying
		 * @return string
		 */
		public function display_shortcode( $url, $orig_url, $context ) {
			if ( $context == 'display' && $this->has_shortcode( $orig_url ) ) {
				return do_shortcode( $orig_url );
			}
			return $url;
		}

		/**
		 * Ajax handler for add menu item request
		 */
		public function ajax_add_menu_item() {

			check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

			if ( !current_user_can( 'edit_theme_options' ) )
				wp_die( -1 );

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
			if ( is_wp_error( $item_ids ) )
				wp_die( 0 );

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

	}

	$gs_sim		 = new gsShortCodeInMenu();
	$gs_sim_init = $gs_sim->init();
}

