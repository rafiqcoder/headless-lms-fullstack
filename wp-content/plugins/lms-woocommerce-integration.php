<?php
/**
 * Plugin Name: LMS WooCommerce Integration
 * Description: Integrates LMS courses with WooCommerce for paid course sales
 * Version: 1.0.0
 * Author: Headless LMS Team
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class LMS_WooCommerce_Integration {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }
    
    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Add WooCommerce hooks
        add_action('woocommerce_order_status_completed', array($this, 'auto_enroll_user_on_purchase'), 10, 1);
        add_action('woocommerce_payment_complete', array($this, 'auto_enroll_user_on_payment'), 10, 1);
        
        // Handle checkout redirect
        add_action('woocommerce_thankyou', array($this, 'handle_checkout_redirect'), 10, 1);
        
        // Create course products if they don't exist
        add_action('admin_init', array($this, 'create_default_course_products'));
    }
    
    public function woocommerce_missing_notice() {
        ?>
        <div class="error notice">
            <p><?php _e('LMS WooCommerce Integration requires WooCommerce to be installed and active.', 'lms-woocommerce'); ?></p>
        </div>
        <?php
    }
    
    public function create_default_course_products() {
        // Only run once
        if (get_option('lms_default_products_created')) {
            return;
        }
        
        // Create product for course 13
        $this->create_course_product(13, 'Advanced Web Development Course', 49.99);
        
        // Mark as created
        update_option('lms_default_products_created', true);
    }
    
    private function create_course_product($course_id, $course_name, $price) {
        // Check if course exists
        $course = get_post($course_id);
        if (!$course) {
            return false;
        }
        
        // Check if product already exists
        $existing_product = get_posts(array(
            'post_type' => 'product',
            'meta_query' => array(
                array(
                    'key' => '_course_id',
                    'value' => $course_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($existing_product)) {
            return $existing_product[0]->ID;
        }
        
        // Create new product
        $product = new WC_Product_Simple();
        $product->set_name($course_name);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_description('Access to the ' . $course_name . ' course materials and content');
        $product->set_short_description('Full course access with lifetime updates');
        $product->set_sku('COURSE-' . $course_id);
        $product->set_price($price);
        $product->set_regular_price($price);
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');
        $product->set_virtual(true);
        $product->set_downloadable(false);
        
        $product_id = $product->save();
        
        if ($product_id) {
            // Link product to course
            update_post_meta($product_id, '_course_id', $course_id);
            update_post_meta($product_id, '_auto_enroll', 'yes');
            
            return $product_id;
        }
        
        return false;
    }
    
    public function auto_enroll_user_on_purchase($order_id) {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        
        if (!$user_id) {
            return; // Guest checkout - would need to handle differently
        }
        
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            
            if ($product) {
                $course_id = get_post_meta($product->get_id(), '_course_id', true);
                $auto_enroll = get_post_meta($product->get_id(), '_auto_enroll', true);
                
                if ($course_id && $auto_enroll === 'yes') {
                    $this->enroll_user_in_course($user_id, $course_id, $order_id);
                }
            }
        }
    }
    
    public function auto_enroll_user_on_payment($order_id) {
        $this->auto_enroll_user_on_purchase($order_id);
    }
    
    private function enroll_user_in_course($user_id, $course_id, $order_id) {
        // Store in the format expected by Next.js API
        $enrollments = get_user_meta($user_id, 'lms_course_enrollments', true);
        if (!is_array($enrollments)) {
            $enrollments = array();
        }
        
        // Check if already enrolled
        foreach ($enrollments as $enrollment) {
            if ($enrollment['course_id'] == $course_id) {
                return; // Already enrolled
            }
        }
        
        $enrollments[] = array(
            'course_id' => $course_id,
            'enrolled_date' => current_time('mysql'),
            'status' => 'active',
            'order_id' => $order_id
        );
        
        update_user_meta($user_id, 'lms_course_enrollments', $enrollments);
        
        // Trigger action for other plugins to hook into
        do_action('lms_user_enrolled_in_course', $user_id, $course_id, $order_id);
    }
    
    public function handle_checkout_redirect($order_id) {
        if (!$order_id) return;
        
        $redirect_url = isset($_GET['redirect_to']) ? urldecode($_GET['redirect_to']) : '';
        
        if ($redirect_url && filter_var($redirect_url, FILTER_VALIDATE_URL)) {
            // Add a delay and redirect via JavaScript
            echo '<script>
                setTimeout(function() {
                    window.location.href = "' . esc_url($redirect_url) . '";
                }, 3000);
            </script>';
            echo '<div class="woocommerce-message">Thank you for your purchase! Redirecting you back to the course...</div>';
        }
    }
    
    public function register_rest_routes() {
        register_rest_route('lms/v1', '/course/(?P<course_id>\d+)/product', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_course_product'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('lms/v1', '/course/(?P<course_id>\d+)/checkout', array(
            'methods' => 'POST',
            'callback' => array($this, 'rest_create_checkout_url'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('lms/v1', '/user/(?P<user_id>\d+)/enrollments', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_user_enrollments'),
            'permission_callback' => '__return_true'
        ));
    }
    
    public function rest_get_course_product($request) {
        $course_id = $request['course_id'];
        
        $products = get_posts(array(
            'post_type' => 'product',
            'meta_query' => array(
                array(
                    'key' => '_course_id',
                    'value' => $course_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (empty($products)) {
            return new WP_Error('no_product', 'No product found for this course', array('status' => 404));
        }
        
        $product = wc_get_product($products[0]->ID);
        
        return rest_ensure_response(array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'currency' => get_woocommerce_currency(),
            'sku' => $product->get_sku(),
            'course_id' => $course_id,
            'add_to_cart_url' => add_query_arg('add-to-cart', $product->get_id(), wc_get_cart_url()),
            'checkout_url' => add_query_arg('add-to-cart', $product->get_id(), wc_get_checkout_url())
        ));
    }
    
    public function rest_create_checkout_url($request) {
        $course_id = $request['course_id'];
        $data = $request->get_json_params();
        $user_id = $data['user_id'] ?? null;
        $redirect_url = $data['redirect_url'] ?? '';
        
        $products = get_posts(array(
            'post_type' => 'product',
            'meta_query' => array(
                array(
                    'key' => '_course_id',
                    'value' => $course_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (empty($products)) {
            return new WP_Error('no_product', 'No product found for this course', array('status' => 404));
        }
        
        $product = wc_get_product($products[0]->ID);
        
        // Create checkout URL with product added to cart
        $checkout_url = add_query_arg(array(
            'add-to-cart' => $product->get_id()
        ), wc_get_checkout_url());
        
        if ($redirect_url) {
            $checkout_url = add_query_arg('redirect_to', urlencode($redirect_url), $checkout_url);
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'checkout_url' => $checkout_url,
            'product' => array(
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'currency' => get_woocommerce_currency()
            )
        ));
    }
    
    public function rest_get_user_enrollments($request) {
        $user_id = $request['user_id'];
        
        // Get user enrollments from user meta
        $enrollments = get_user_meta($user_id, 'lms_course_enrollments', true);
        if (!is_array($enrollments)) {
            $enrollments = array();
        }
        
        return rest_ensure_response(array(
            'user_id' => $user_id,
            'enrollments' => $enrollments
        ));
    }
}

// Initialize the plugin
new LMS_WooCommerce_Integration();
?>
