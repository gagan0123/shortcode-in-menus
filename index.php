<?php
/*
  Plugin Name: Shortcodes in Menus
  Description: Allows you to add shortcodes in WordPress Navigation Menus
  Plugin URI: http://wordpress.org/plugins/shortcode-in-menus/
  Version: 1.1
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

function gs_wp_nav_menu_setup() {
    add_meta_box('add-html-section', __('HTML Section'), 'gs_wp_nav_menu_item_link_meta_box', 'nav-menus', 'side', 'default');

    // Register advanced menu items (columns)
    add_filter('manage_nav-menus_columns', 'gs_wp_nav_menu_manage_columns');
}

add_action('admin_init', 'gs_wp_nav_menu_setup');

function gs_wp_nav_menu_item_link_meta_box() {
    global $_nav_menu_placeholder, $nav_menu_selected_id;

    $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
    ?>
    <div class="customhtmldiv" id="customhtmldiv">
        <input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />
        <p id="menu-item-html-wrap">
            <label class="howto" for="custom-menu-item-html">
                <span><?php _e('URL'); ?></span>
                <input id="custom-menu-item-html" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-html]" type="text" class="code menu-item-textbox" value="" />
            </label>
        </p>

        <p id="menu-item-title-wrap">
            <label class="howto" for="custom-menu-item-title">
                <span><?php _e('Section Title'); ?></span>
                <input id="custom-menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox input-with-default-title" title="<?php esc_attr_e('Section Title'); ?>" />
            </label>
        </p>

        <p class="button-controls">
            <span class="add-to-menu">
                <input type="submit"<?php wp_nav_menu_disabled_check($nav_menu_selected_id); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-menu-item" id="submit-customhtmldiv" />
                <span class="spinner"></span>
            </span>
        </p>

    </div><!-- /.customlinkdiv -->
    <?php
}

function gs_wp_nav_menu_manage_columns() {
    
}

function gs_scripts_method($hook) {
    if ('nav-menus.php' != $hook)
        return;
    wp_enqueue_script(
            'gs-sim-admin', plugins_url('/js/admin.js', __FILE__), array('jquery')
    );
}

add_action('admin_enqueue_scripts', 'gs_scripts_method');


function gs_sim_menu_item($item_output,$item){
    if($item->post_title =='FULL HTML OUTPUT'){
        $item_output = do_shortcode($item->url);
    }
    return $item_output;
}
add_filter('walker_nav_menu_start_el','gs_sim_menu_item',10,2);