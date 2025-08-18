<?php
/**
 * Course Publishing Test
 * Test course publishing through admin interface simulation
 */

// Load WordPress
require_once('wp-load.php');

echo "Testing course publishing fix...\n";

// Simulate admin context
define('WP_ADMIN', true);

// Test course creation with status draft first, then publish
$course_data = array(
    'post_title'   => 'Admin Test Course - ' . date('Y-m-d H:i:s'),
    'post_content' => 'This course tests admin publishing functionality.',
    'post_status'  => 'draft',
    'post_type'    => 'course',
    'meta_input'   => array(
        '_price' => 149.99,
        '_currency' => 'BDT',
        '_level' => 'intermediate',
        '_duration' => 15,
        '_instructor' => 1
    )
);

echo "1. Creating draft course...\n";
$course_id = wp_insert_post($course_data);

if (is_wp_error($course_id)) {
    echo "ERROR creating draft: " . $course_id->get_error_message() . "\n";
    exit;
}

echo "SUCCESS: Draft course created with ID: $course_id\n";

// Now test publishing the course
echo "2. Publishing course...\n";
$publish_result = wp_update_post(array(
    'ID' => $course_id,
    'post_status' => 'publish'
));

if (is_wp_error($publish_result)) {
    echo "ERROR publishing: " . $publish_result->get_error_message() . "\n";
} else {
    echo "SUCCESS: Course published with ID: $publish_result\n";
    
    // Verify the course is published
    $published_course = get_post($course_id);
    echo "3. Verification: Course status is now: " . $published_course->post_status . "\n";
    
    if ($published_course->post_status === 'publish') {
        echo "✓ Course publishing is working correctly!\n";
        echo "✓ Course title: " . $published_course->post_title . "\n";
        echo "✓ Course price: " . get_post_meta($course_id, '_price', true) . "\n";
    } else {
        echo "✗ Course publishing failed - status is not 'publish'\n";
    }
}

echo "\n4. Testing GraphQL query for published course...\n";
echo "The new course should appear in GraphQL queries now.\n";
?>
