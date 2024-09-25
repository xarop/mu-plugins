<?php
/*
Plugin Name: Xarop Cleanup
Description: Disables Gutenberg editor, comments, emojis, and unnecessary meta from the header. Replaces WordPress logo on login/register page with a custom logo.
Version: 1.1
Author: xarop.com
*/

// Disable Gutenberg editor
// add_filter('use_block_editor_for_post', '__return_false', 10);

// Disable comments
add_action('init', function () {
    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }

    // Close comments on the front-end
    add_filter('comments_open', '__return_false', 20, 2);
    add_filter('pings_open', '__return_false', 20, 2);

    // Hide existing comments
    add_filter('comments_array', '__return_empty_array', 10, 2);

    // Remove comments page in menu
    add_action('admin_menu', function () {
        remove_menu_page('edit-comments.php');
    });

    // Redirect any user trying to access comments page
    add_action('admin_init', function () {
        if (isset($_GET['page']) && $_GET['page'] === 'edit-comments.php') {
            wp_redirect(admin_url());
            exit;
        }
    });
});

// Disable Emojis
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

// Remove unnecessary header meta
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');

// Custom Login Logo
function xarop_custom_login_logo()
{ ?>
    <style type="text/css">
        #login h1 a,
        .login h1 a {
            background-image: url('<?php echo plugin_dir_url(__FILE__) . 'xarop-logo.svg'; ?>');
            height: 62px;
            width: 160px;
            background-size: contain;
            background-repeat: no-repeat;
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action('login_enqueue_scripts', 'xarop_custom_login_logo');

// Change Login Logo URL
function xarop_custom_login_url()
{
    return home_url();
}
add_filter('login_headerurl', 'xarop_custom_login_url');

// Change Login Logo Title
function xarop_custom_login_title()
{
    return get_option('blogname');
}
add_filter('login_headertitle', 'xarop_custom_login_title');


// Disable default WordPress dashboard widgets
function xarop_disable_default_dashboard_widgets()
{
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');   // Activity Widget
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Draft Widget
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');  // At a Glance Widget
    remove_meta_box('dashboard_primary', 'dashboard', 'side');      // WordPress Events and News Widget
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');    // Secondary Widget
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal'); // Site Health Widget
}
add_action('wp_dashboard_setup', 'xarop_disable_default_dashboard_widgets');

// Add custom dashboard widget with iframe
function xarop_add_custom_dashboard_widget()
{
    wp_add_dashboard_widget('xarop_custom_widget', 'Developed in Barcelona by xarop.com', 'xarop_custom_dashboard_content');
}
add_action('wp_dashboard_setup', 'xarop_add_custom_dashboard_widget');

function xarop_custom_dashboard_content()
{
    $site_name = get_bloginfo('name');
    $site_url = get_site_url();
    echo '<h3>' . $site_name . '</h3>';
    echo '<small>' . $site_url . '</small>';
    echo '<iframe src="//xarop.com?site=' . esc_attr($site_url) . '" style="width:100%; height:500px; border:none;"></iframe>';
    //echo 'Developed in Barcelona by xarop.com <br/><br/><a href="https://xarop.com/" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'xarop-logo.svg' . '" style="width:100%; height:100px; border:none;"></a>';
}
