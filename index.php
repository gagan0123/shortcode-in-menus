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

/*
 * Legacy code, used custom links metabox
 */

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
/*
 * Legacy code ends (more or less)
 */

/**
 * Adding a test shortcode for testing this plugin
 */
add_shortcode('gs_test_shortcode', 'gs_sim_test_shortcode');

/**
 * Returns Developer's website for testing the shortcode
 */
function gs_sim_test_shortcode($data) {
    return "http://gagan.pro";
}

/**
 * Add custom meta box to edit menu screen
 */
function gs_wp_nav_menu_setup() {
    add_meta_box('add-html-section', __('Shortcode'), 'gs_wp_nav_menu_item_link_meta_box', 'nav-menus', 'side', 'default');
}

add_action('admin_init', 'gs_wp_nav_menu_setup');

function gs_wp_nav_menu_item_link_meta_box() {
    global $_nav_menu_placeholder, $nav_menu_selected_id;

    $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
    
    $last_object_id = get_option('gs_sim_last_object_id',0);
    $object_id = gs_sim_increase_object_id($last_object_id);
    ?>
    <div class="gs-sim-div" id="gs-sim-div">
        <input type="hidden" class="menu-item-db-id" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-db-id]" value="0" />
        <input type="hidden" class="menu-item-object-id" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object-id]" value="<?php echo $object_id; ?>" />
        <input type="hidden" class="menu-item-object" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-object]" value="gs_sim" />
        <input type="hidden" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" value="gs_sim" />
        <input type="hidden" id="gs-sim-description-nonce" value="<?php echo wp_create_nonce('gs-sim-description-nonce')?>" />
        <p id="menu-item-title-wrap">
            <label for="gs-sim-title"><?php _e('Title'); ?></label>
            <input id="gs-sim-title" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox" title="<?php esc_attr_e('Title'); ?>" style="width:100%" />    
        </p>
        
        <p id="menu-item-html-wrap">
            <textarea style="width:100%;" rows="9" id="gs-sim-html" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-description]" class="code menu-item-textbox" title="<?php esc_attr_e('Text/html/shortcode here!'); ?>"></textarea>
        </p>

        <p class="button-controls">
            <span class="add-to-menu">
                <input type="submit"<?php wp_nav_menu_disabled_check($nav_menu_selected_id); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-gs-sim-menu-item" id="submit-gs-sim" />
                <span class="spinner"></span>
            </span>
        </p>

    </div><!-- /.customlinkdiv -->
    <?php
}

function gs_sim_increase_object_id($last_object_id){
    $object_id = (int)$last_object_id;
    $object_id++;
    $object_id = ($object_id<1)? 1 : $object_id;
    update_option('gs_sim_last_object_id', $object_id);
    return $object_id;
}
function gs_scripts_method($hook) {
    if ('nav-menus.php' != $hook)
        return;
    wp_enqueue_script(
            'gs-sim-admin', plugins_url('/js/admin.js', __FILE__), array('nav-menu')
    );
}

add_action('admin_enqueue_scripts', 'gs_scripts_method');


add_filter('walker_nav_menu_start_el', 'gs_sim_menu_item', 10, 2);

function gs_sim_menu_item($item_output, $item) {
    
    if ($item->object != 'gs_sim') {
        // legacy support
        if ($item->post_title == 'FULL HTML OUTPUT') {
            $item_output = do_shortcode($item->url);
        }
        return $item_output;
    }
    

    if ($item->object == 'gs_sim') {
        $item_output = do_shortcode($item->description);
        return $item_output;
    }
    return $item_output;
}

add_filter( 'wp_setup_nav_menu_item',  'gs_setup_nav_menu_item', 10, 1);

function gs_setup_nav_menu_item ($item){
    if($item->object == 'gs_sim'){
        
        // setup our label
        $item->type_label =  __('Shortcode', 'gs_sim');
        $item->description = get_transient('gs_sim_description_hack_'.$item->object_id);
        delete_transient('gs_sim_description_hack_'.$item->object_id);
    }
    return $item;
}

add_action('wp_ajax_gs_sim_description_hack','gs_sim_description_hack');

function gs_sim_description_hack(){
    $nonce  = $_POST['description-nonce'];
    if ( ! wp_verify_nonce( $nonce, 'gs-sim-description-nonce' ) ) {
        die();
    }
    $item   = $_POST['menu-item'];
    set_transient('gs_sim_description_hack_'.$item['menu-item-object-id'], $item['menu-item-description']);
    $object_id = gs_sim_increase_object_id($item['menu-item-object-id']);
    echo $object_id;
    die();
}