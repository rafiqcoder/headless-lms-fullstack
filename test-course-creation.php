<?php
/**
 * Test Course Creation
 * Simple test to create a course and check for issues
 */

// Load WordPress
require_once('wp-load.php');

// Test course creation
$course_data = array(
    'post_title'   => 'Test Course - ' . date('Y-m-d H:i:s'),
    'post_content' => 'This is a test course content.',
    'post_status'  => 'publish',
    'post_type'    => 'course',
    'meta_input'   => array(
        '_price' => 99.99,
        '_currency' => 'BDT',
        '_level' => 'beginner',
        '_duration' => 10,
        '_instructor' => 1
    )
);

echo "Testing course creation...\n";

$course_id = wp_insert_post($course_data);

if (is_wp_error($course_id)) {
    echo "ERROR: " . $course_id->get_error_message() . "\n";
} else {
    echo "SUCCESS: Course created with ID: $course_id\n";
    
    // Test GraphQL query for this course
    echo "Testing GraphQL query...\n";
    
    $query = '{
        courses {
            edges {
                node {
                    id
                    title
                    price
                    currency
                    level
                }
            }
        }
    }';
    
    echo "GraphQL Query: $query\n";
    echo "Course should be visible in GraphQL now.\n";
}
?>
