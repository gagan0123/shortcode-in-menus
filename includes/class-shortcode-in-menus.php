<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Shortcode_In_Menus' ) ) {

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
			if ( null == self::$instance ) {
				self::$instance = new self;
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

			// register a test shortcode for testing
			add_shortcode( 'gs_test_shortcode', array( $this, 'shortcode' ) );

			// filter the menu item output on frontend
			add_filter( 'walker_nav_menu_start_el', array( $this, 'start_el' ), 10, 2 );

			// filter the output when shortcode is saved using custom links, for legacy support
			add_filter( 'clean_url', array( $this, 'display_shortcode' ), 1, 3 );

			// filter the menu item before display in admin
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
		 * Gets a new object ID, given the current one
		 * 
		 * @since 2.0
		 * @access public
		 * 
		 * @param int $last_object_id The current/last object id
		 * 
		 * @return int Returns new object ID.
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
		 * Display our custom meta box.
		 * 
		 * @since 2.0
		 * @access public
		 * 
		 * @global int $_nav_menu_placeholder        A placeholder index for the menu item
		 * @global int|string $nav_menu_selected_id  (id, name or slug) of the currently-selected menu
		 * 
		 * @return void
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
					<textarea style="width:100%;" rows="9" id="gs-sim-html" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-description]" class="code menu-item-textbox" title="<?php esc_attr_e( 'Text/HTML/shortcode here!', 'shortcode-in-menus' ); ?>"></textarea>
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

				if ( !empty( $matches ) ) {
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
		 * Allows shortcode to be processed and displayed.
		 * 
		 * @since 1.0
		 * 
		 * @param string $url		The processed URL for displaying/saving.
		 * @param string $orig_url	The URL that was submitted, retrieved.
		 * @param string $context	Whether saving or displaying.
		 * 
		 * @return string Output string after shortcode has been executed.
		 */
		public function display_shortcode( $url, $orig_url, $context ) {
			if ( $context == 'display' && $this->has_shortcode( $orig_url ) ) {
				return do_shortcode( $orig_url );
			}
			return $url;
		}

		/**
		 * Modify the menu item before display on Menu editor.
		 * 
		 * @since 2.0
		 * @access public
		 * 
		 * @param object $item The menu item.
		 * 
		 * @return object Modified menu item object.
		 */
		public function setup_item( $item ) {
			if ( !is_object( $item ) ) {
				return $item;
			}

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

	}

}
