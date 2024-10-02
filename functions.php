<?php

/**
 * Plugin Name: SEO Optimized Posts
 * Description: A WordPress plugin for interacting with the OpenAI GPT API to generate SEO optimized blog posts.
 * Version: 1.0
 * Author: Coastal Web Solutions
 * Author URI: https://coastalwebsolutions.agency/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! function_exists( 'aab_fs' ) ) {
    // Create a helper function for easy SDK access.
    function aab_fs() {
        global $aab_fs;

        if ( ! isset( $aab_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $aab_fs = fs_dynamic_init( array(
                'id'                  => '16419',
                'slug'                => 'ai-auto-blog',
                'premium_slug'        => 'ai-blog',
                'type'                => 'plugin',
                'public_key'          => 'pk_d3643ba3fed3cb98e8973152f6175',
                'is_premium'          => true,
                'premium_suffix'      => 'Personal Blogger',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'ai-post',
                    'first-path'     => 'admin.php?page=ai-post',
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $aab_fs;
    }

    // Init Freemius.
    aab_fs();
    // Signal that SDK was initiated.
    do_action( 'aab_fs_loaded' );
}

// Enqueue JavaScript file for AJAX

function gpt_chat_enqueue_scripts() {

    wp_enqueue_style('ai_optimize_posts_style', plugin_dir_url(__FILE__) . 'assets/main_style.css', array(), '1.0');

    // Enqueue jQuery with 'defer' attribute

    wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js', array(), '3.6.0', true);



    // Enqueue Typed.js with 'defer' attribute

    wp_enqueue_script('typejs', 'https://unpkg.com/typed.js@2.1.0/dist/typed.min.js', array(), '2.1.0', true);



    // Enqueue your main script with 'defer' attribute

    wp_enqueue_script('chat_gpt', plugin_dir_url(__FILE__) . 'assets/main.js', array('jquery'), '1.0', true);

   

    // Localize your main script

    wp_localize_script('chat_gpt', 'gpt_chat_ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

    wp_enqueue_script('jquery-datatables', 'https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js', array('jquery'), '1.11.5', true);

    // Enqueue DataTables CSS
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css', array(), '1.11.5');

    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery'), '', true);

    wp_register_script('ckeditor', 'https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js', array(), '41.3.1', true);
    
    // Enqueue CKEditor script
    wp_enqueue_script('ckeditor');

    wp_enqueue_script('jquery-validation', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.js', array('jquery'), '1.19.3', true);
    wp_enqueue_script('font-awesome', 'https://kit.fontawesome.com/80ce2e9fd6.js', array(), '5.15.4');

}

add_action('admin_enqueue_scripts', 'gpt_chat_enqueue_scripts');





// Register function to render admin page and add top-level menu page
function ai_post_admin_page() {
    add_menu_page(
        'SEO Optimized Posts',            // Page title
        'SEO Optimized Posts',                      // Menu title
        'manage_options',                 // Capability required to access
        'ai-post',                        // Menu slug
        function () {                     // Function to render the page
            require_once plugin_dir_path(__FILE__) .  'seo-optimized-posts.php';

        },
        'dashicons-admin-generic',        // Icon
        90,
                                        
    );
    add_submenu_page(
        'ai-post',                        // Parent slug
        '',                               // Page title (empty string to remove the submenu item)
        '',                               // Menu title (empty string to remove the submenu item)
        'manage_options',                // Capability required to access
        'ai-post',                       // Menu slug
        ''               // Callback function to render the page
    );
    
    add_submenu_page(
        'ai-post',                        // Parent slug
        'How to Use',                       // Page title
        'How to Use',                       // Menu title
        'manage_options',                // Capability required to access
        'ai-how-to-use',                  // Menu slug
        'ai_how_to_use_callback'          // Callback function to render the page
    );

    add_submenu_page(
        'ai-post',                        // Parent slug
        'My Posts',                       // Page title
        'My Posts',                       // Menu title
        'manage_options',                // Capability required to access
        'ai-all-posts',                  // Menu slug
        'ai_all_posts_callback'          // Callback function to render the page
    );

    add_submenu_page(
        'ai-post',                        // Parent slug
        'Create a Post',                   // Page title
        'Create a Post',                   // Menu title
        'manage_options',                // Capability required to access
        'ai-create-post',                // Menu slug
        'ai_create_post_callback'        // Callback function to render the page
    );

    add_submenu_page(
        'ai-post',                        // Parent slug
        'Categories',             // Page title
        'Categories',             // Menu title
        'manage_options',                // Capability required to access
        'ai-manage-categories',          // Menu slug
        'ai_manage_categories_callback'  // Callback function to render the page
    );
    
      add_submenu_page(
        'ai-post',                        // Parent slug
        'License',                       // Page title
        'License',                       // Menu title
        'manage_options',                // Capability required to access
        'ai-license',                  // Menu slug
        'ai_license_callback'          // Callback function to render the page
    );
}

add_action('admin_menu', 'ai_post_admin_page');
// Callback functions for submenus
function ai_post_listing_callback() {
    include_once(plugin_dir_path(__FILE__) . 'templates/ai_post_listing-page.php');
}
function ai_all_posts_callback() {
    include_once(plugin_dir_path(__FILE__) . 'seo-optimized-posts.php');
}

function ai_create_post_callback() {
    include_once(plugin_dir_path(__FILE__) . 'templates/ai_create_post-page.php');
}

function ai_manage_categories_callback() {
    include_once(plugin_dir_path(__FILE__) . 'templates/ai_posts_category-page.php');
}
function ai_how_to_use_callback() {
     if(aab_fs()->can_use_premium_code()){
        echo '<h2>How to use</h2>';
     }
}
function ai_license_callback() {
     if(aab_fs()->can_use_premium_code()){
        echo '<h2>License</h2>';
     }
}


function custom_edit_post_template($template) {
    if (isset($_GET['page']) && $_GET['page'] == 'ai-edit-post') {
        
        return plugin_dir_path(__FILE__) . 'templates/ai_edit_post-page.php';
    }
    return $template;
}
add_filter('template_include', 'custom_edit_post_template',99);

// AJAX handler for form submission
require_once plugin_dir_path(__FILE__) .  'templates/form_handler.php';




