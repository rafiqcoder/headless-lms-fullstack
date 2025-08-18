<?php
/**
 * Headless LMS Theme Functions
 * Essential functions for headless WordPress setup
 */

// Remove unnecessary WordPress features for headless setup
function headless_lms_cleanup() {
    // Remove theme support for features not needed in headless
    remove_theme_support('custom-header');
    remove_theme_support('custom-background');
    
    // Disable feeds
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    
    // Disable WordPress version in head
    remove_action('wp_head', 'wp_generator');
    
    // Remove RSD link
    remove_action('wp_head', 'rsd_link');
    
    // Remove Windows Live Writer
    remove_action('wp_head', 'wlwmanifest_link');
    
    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
    
    // Remove emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('after_setup_theme', 'headless_lms_cleanup');

// Enable CORS for REST API
function headless_lms_cors() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-WP-Nonce');
    header('Access-Control-Allow-Credentials: true');
}
add_action('rest_api_init', 'headless_lms_cors');

// Handle preflight requests
function headless_lms_handle_preflight() {
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        headless_lms_cors();
        exit(0);
    }
}
add_action('init', 'headless_lms_handle_preflight');

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Remove admin bar for all users on frontend (not needed in headless)
add_filter('show_admin_bar', '__return_false');

// Enqueue minimal admin styles
function headless_lms_admin_styles() {
    if (is_admin()) {
        wp_enqueue_style('headless-lms-admin', get_template_directory_uri() . '/admin.css');
    }
}
add_action('admin_enqueue_scripts', 'headless_lms_admin_styles');
?>
