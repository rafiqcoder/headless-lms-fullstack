<?php
/**
 * WPGraphQL LMS Extension
 * Extends WPGraphQL with custom LMS types and fields
 */

/*
Plugin Name: WPGraphQL LMS Extension
Description: Extends WPGraphQL with custom Learning Management System types and fields
Version: 1.0
Requires Plugins: wp-graphql
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if WPGraphQL is active
add_action('admin_init', function() {
    if (!class_exists('WPGraphQL')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>WPGraphQL LMS Extension requires WPGraphQL plugin to be installed and activated.</p></div>';
        });
        deactivate_plugins(plugin_basename(__FILE__));
    }
});

class WPGraphQLLMSExtension {
    
    public function __construct() {
        add_action('graphql_register_types', array($this, 'register_types'));
        add_action('init', array($this, 'register_post_types'));
    }
    
    /**
     * Register custom post types for LMS
     */
    public function register_post_types() {
        // Register Course post type
        register_post_type('course', array(
            'label' => 'Courses',
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'course',
            'graphql_plural_name' => 'courses',
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'menu_icon' => 'dashicons-book-alt',
            'labels' => array(
                'name' => 'Courses',
                'singular_name' => 'Course',
                'add_new_item' => 'Add New Course',
                'edit_item' => 'Edit Course',
                'new_item' => 'New Course',
                'view_item' => 'View Course',
                'search_items' => 'Search Courses',
                'not_found' => 'No courses found',
                'not_found_in_trash' => 'No courses found in trash'
            )
        ));
        
        // Register Lesson post type
        register_post_type('lesson', array(
            'label' => 'Lessons',
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'lesson',
            'graphql_plural_name' => 'lessons',
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes'),
            'menu_icon' => 'dashicons-video-alt3',
            'labels' => array(
                'name' => 'Lessons',
                'singular_name' => 'Lesson',
                'add_new_item' => 'Add New Lesson',
                'edit_item' => 'Edit Lesson',
                'new_item' => 'New Lesson',
                'view_item' => 'View Lesson',
                'search_items' => 'Search Lessons',
                'not_found' => 'No lessons found',
                'not_found_in_trash' => 'No lessons found in trash'
            )
        ));
        
        // Register Quiz post type
        register_post_type('quiz', array(
            'label' => 'Quizzes',
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'show_in_graphql' => true,
            'graphql_single_name' => 'quiz',
            'graphql_plural_name' => 'quizzes',
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-forms',
            'labels' => array(
                'name' => 'Quizzes',
                'singular_name' => 'Quiz',
                'add_new_item' => 'Add New Quiz',
                'edit_item' => 'Edit Quiz',
                'new_item' => 'New Quiz',
                'view_item' => 'View Quiz',
                'search_items' => 'Search Quizzes',
                'not_found' => 'No quizzes found',
                'not_found_in_trash' => 'No quizzes found in trash'
            )
        ));
    }
    
    /**
     * Register GraphQL types and fields
     */
    public function register_types() {
        // Register Course meta fields
        register_graphql_fields('Course', array(
            'price' => array(
                'type' => 'Float',
                'description' => 'Course price',
                'resolve' => function($course) {
                    return (float) get_post_meta($course->ID, '_price', true) ?: 0;
                }
            ),
            'currency' => array(
                'type' => 'String',
                'description' => 'Course currency',
                'resolve' => function($course) {
                    return get_post_meta($course->ID, '_currency', true) ?: 'BDT';
                }
            ),
            'level' => array(
                'type' => 'String',
                'description' => 'Course difficulty level',
                'resolve' => function($course) {
                    return get_post_meta($course->ID, '_level', true) ?: 'beginner';
                }
            ),
            'duration' => array(
                'type' => 'Int',
                'description' => 'Course duration in hours',
                'resolve' => function($course) {
                    return (int) get_post_meta($course->ID, '_duration', true) ?: 0;
                }
            ),
            'instructor' => array(
                'type' => 'User',
                'description' => 'Course instructor',
                'resolve' => function($course) {
                    $instructor_id = get_post_meta($course->ID, '_instructor', true) ?: $course->post_author;
                    return get_user_by('ID', $instructor_id);
                }
            ),
            'enrollmentCount' => array(
                'type' => 'Int',
                'description' => 'Number of enrolled students',
                'resolve' => function($course) {
                    return (int) get_post_meta($course->ID, '_enrollment_count', true) ?: 0;
                }
            ),
            'isPublished' => array(
                'type' => 'Boolean',
                'description' => 'Whether the course is published',
                'resolve' => function($course) {
                    return $course->post_status === 'publish';
                }
            ),
            'lessons' => array(
                'type' => array('list_of' => 'Lesson'),
                'description' => 'Course lessons',
                'resolve' => function($course) {
                    $lessons = get_posts(array(
                        'post_type' => 'lesson',
                        'meta_query' => array(
                            array(
                                'key' => '_course_id',
                                'value' => $course->ID,
                                'compare' => '='
                            )
                        ),
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                        'posts_per_page' => -1
                    ));
                    return $lessons;
                }
            )
        ));
        
        // Register Lesson meta fields
        register_graphql_fields('Lesson', array(
            'courseId' => array(
                'type' => 'ID',
                'description' => 'Associated course ID',
                'resolve' => function($lesson) {
                    return get_post_meta($lesson->ID, '_course_id', true);
                }
            ),
            'videoUrl' => array(
                'type' => 'String',
                'description' => 'Lesson video URL',
                'resolve' => function($lesson) {
                    return get_post_meta($lesson->ID, '_video_url', true);
                }
            ),
            'duration' => array(
                'type' => 'Int',
                'description' => 'Lesson duration in minutes',
                'resolve' => function($lesson) {
                    return (int) get_post_meta($lesson->ID, '_duration', true) ?: 0;
                }
            ),
            'order' => array(
                'type' => 'Int',
                'description' => 'Lesson order in course',
                'resolve' => function($lesson) {
                    return (int) $lesson->menu_order;
                }
            ),
            'course' => array(
                'type' => 'Course',
                'description' => 'Associated course',
                'resolve' => function($lesson) {
                    $course_id = get_post_meta($lesson->ID, '_course_id', true);
                    return $course_id ? get_post($course_id) : null;
                }
            )
        ));
        
        // Register Quiz meta fields
        register_graphql_fields('Quiz', array(
            'courseId' => array(
                'type' => 'ID',
                'description' => 'Associated course ID',
                'resolve' => function($quiz) {
                    return get_post_meta($quiz->ID, '_course_id', true);
                }
            ),
            'timeLimit' => array(
                'type' => 'Int',
                'description' => 'Quiz time limit in minutes',
                'resolve' => function($quiz) {
                    return (int) get_post_meta($quiz->ID, '_time_limit', true) ?: 0;
                }
            ),
            'passingScore' => array(
                'type' => 'Int',
                'description' => 'Minimum score to pass',
                'resolve' => function($quiz) {
                    return (int) get_post_meta($quiz->ID, '_passing_score', true) ?: 70;
                }
            ),
            'questions' => array(
                'type' => 'String',
                'description' => 'Quiz questions as JSON',
                'resolve' => function($quiz) {
                    return get_post_meta($quiz->ID, '_questions', true) ?: '[]';
                }
            ),
            'course' => array(
                'type' => 'Course',
                'description' => 'Associated course',
                'resolve' => function($quiz) {
                    $course_id = get_post_meta($quiz->ID, '_course_id', true);
                    return $course_id ? get_post($course_id) : null;
                }
            )
        ));
        
        // Register Enrollment type
        register_graphql_object_type('Enrollment', array(
            'description' => 'Student course enrollment',
            'fields' => array(
                'id' => array('type' => 'ID'),
                'studentId' => array('type' => 'ID'),
                'courseId' => array('type' => 'ID'),
                'enrolledAt' => array('type' => 'String'),
                'status' => array('type' => 'String'),
                'progress' => array('type' => 'Float'),
                'completedAt' => array('type' => 'String'),
                'student' => array('type' => 'User'),
                'course' => array('type' => 'Course')
            )
        ));
        
        // Register Progress type
        register_graphql_object_type('Progress', array(
            'description' => 'Student lesson progress',
            'fields' => array(
                'id' => array('type' => 'ID'),
                'studentId' => array('type' => 'ID'),
                'lessonId' => array('type' => 'ID'),
                'completed' => array('type' => 'Boolean'),
                'completedAt' => array('type' => 'String'),
                'timeSpent' => array('type' => 'Int'),
                'student' => array('type' => 'User'),
                'lesson' => array('type' => 'Lesson')
            )
        ));
        
        // Register root query fields
        register_graphql_field('RootQuery', 'courseEnrollments', array(
            'type' => array('list_of' => 'Enrollment'),
            'description' => 'Get course enrollments',
            'args' => array(
                'courseId' => array('type' => 'ID'),
                'studentId' => array('type' => 'ID')
            ),
            'resolve' => function($root, $args) {
                global $wpdb;
                
                $where = array('1=1');
                if (!empty($args['courseId'])) {
                    $where[] = $wpdb->prepare('course_id = %d', $args['courseId']);
                }
                if (!empty($args['studentId'])) {
                    $where[] = $wpdb->prepare('student_id = %d', $args['studentId']);
                }
                
                $query = "SELECT * FROM {$wpdb->prefix}lms_enrollments WHERE " . implode(' AND ', $where);
                $enrollments = $wpdb->get_results($query, ARRAY_A);
                
                return array_map(function($enrollment) {
                    return array(
                        'id' => $enrollment['id'],
                        'studentId' => $enrollment['student_id'],
                        'courseId' => $enrollment['course_id'],
                        'enrolledAt' => $enrollment['enrolled_at'],
                        'status' => $enrollment['status'],
                        'progress' => (float) $enrollment['progress'],
                        'completedAt' => $enrollment['completed_at'],
                        'student' => get_user_by('ID', $enrollment['student_id']),
                        'course' => get_post($enrollment['course_id'])
                    );
                }, $enrollments);
            }
        ));
        
        // Register mutations
        register_graphql_mutation('enrollInCourse', array(
            'inputFields' => array(
                'courseId' => array(
                    'type' => array('non_null' => 'ID'),
                    'description' => 'Course ID to enroll in'
                )
            ),
            'outputFields' => array(
                'enrollment' => array('type' => 'Enrollment'),
                'success' => array('type' => 'Boolean'),
                'message' => array('type' => 'String')
            ),
            'mutateAndGetPayload' => function($input, $context) {
                if (!is_user_logged_in()) {
                    return array(
                        'success' => false,
                        'message' => 'You must be logged in to enroll in a course'
                    );
                }
                
                $user_id = get_current_user_id();
                $course_id = $input['courseId'];
                
                // Check if already enrolled
                global $wpdb;
                $existing = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}lms_enrollments WHERE student_id = %d AND course_id = %d",
                    $user_id,
                    $course_id
                ));
                
                if ($existing) {
                    return array(
                        'success' => false,
                        'message' => 'Already enrolled in this course'
                    );
                }
                
                // Create enrollment
                $result = $wpdb->insert(
                    $wpdb->prefix . 'lms_enrollments',
                    array(
                        'student_id' => $user_id,
                        'course_id' => $course_id,
                        'enrolled_at' => current_time('mysql'),
                        'status' => 'active',
                        'progress' => 0
                    ),
                    array('%d', '%d', '%s', '%s', '%f')
                );
                
                if ($result) {
                    $enrollment_id = $wpdb->insert_id;
                    return array(
                        'success' => true,
                        'message' => 'Successfully enrolled in course',
                        'enrollment' => array(
                            'id' => $enrollment_id,
                            'studentId' => $user_id,
                            'courseId' => $course_id,
                            'enrolledAt' => current_time('mysql'),
                            'status' => 'active',
                            'progress' => 0,
                            'student' => get_user_by('ID', $user_id),
                            'course' => get_post($course_id)
                        )
                    );
                } else {
                    return array(
                        'success' => false,
                        'message' => 'Failed to enroll in course'
                    );
                }
            }
        ));
        
        // Add lesson progress update mutation
        register_graphql_mutation('updateLessonProgress', array(
            'inputFields' => array(
                'lessonId' => array(
                    'type' => array('non_null' => 'ID'),
                    'description' => 'Lesson ID'
                ),
                'completed' => array(
                    'type' => 'Boolean',
                    'description' => 'Whether the lesson is completed'
                ),
                'timeSpent' => array(
                    'type' => 'Int',
                    'description' => 'Time spent on lesson in seconds'
                )
            ),
            'outputFields' => array(
                'progress' => array('type' => 'Progress'),
                'success' => array('type' => 'Boolean'),
                'message' => array('type' => 'String')
            ),
            'mutateAndGetPayload' => function($input, $context) {
                if (!is_user_logged_in()) {
                    return array(
                        'success' => false,
                        'message' => 'You must be logged in to update progress'
                    );
                }
                
                $user_id = get_current_user_id();
                $lesson_id = $input['lessonId'];
                $completed = isset($input['completed']) ? $input['completed'] : false;
                $time_spent = isset($input['timeSpent']) ? $input['timeSpent'] : 0;
                
                global $wpdb;
                
                // Insert or update progress
                $existing = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}lms_progress WHERE student_id = %d AND lesson_id = %d",
                    $user_id,
                    $lesson_id
                ));
                
                if ($existing) {
                    // Update existing progress
                    $result = $wpdb->update(
                        $wpdb->prefix . 'lms_progress',
                        array(
                            'completed' => $completed ? 1 : 0,
                            'completed_at' => $completed ? current_time('mysql') : null,
                            'time_spent' => $time_spent
                        ),
                        array('id' => $existing->id),
                        array('%d', '%s', '%d'),
                        array('%d')
                    );
                } else {
                    // Create new progress
                    $result = $wpdb->insert(
                        $wpdb->prefix . 'lms_progress',
                        array(
                            'student_id' => $user_id,
                            'lesson_id' => $lesson_id,
                            'completed' => $completed ? 1 : 0,
                            'completed_at' => $completed ? current_time('mysql') : null,
                            'time_spent' => $time_spent
                        ),
                        array('%d', '%d', '%d', '%s', '%d')
                    );
                }
                
                if ($result !== false) {
                    return array(
                        'success' => true,
                        'message' => 'Progress updated successfully',
                        'progress' => array(
                            'id' => $existing ? $existing->id : $wpdb->insert_id,
                            'studentId' => $user_id,
                            'lessonId' => $lesson_id,
                            'completed' => $completed,
                            'completedAt' => $completed ? current_time('mysql') : null,
                            'timeSpent' => $time_spent,
                            'student' => get_user_by('ID', $user_id),
                            'lesson' => get_post($lesson_id)
                        )
                    );
                } else {
                    return array(
                        'success' => false,
                        'message' => 'Failed to update progress'
                    );
                }
            }
        ));
    }
}

// Initialize the extension
add_action('plugins_loaded', function() {
    if (class_exists('WPGraphQL')) {
        new WPGraphQLLMSExtension();
    }
});

// Create database tables on activation
register_activation_hook(__FILE__, function() {
    global $wpdb;
    
    // Create enrollments table
    $table_name = $wpdb->prefix . 'lms_enrollments';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        student_id bigint(20) UNSIGNED NOT NULL,
        course_id bigint(20) UNSIGNED NOT NULL,
        enrolled_at datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'active',
        progress float DEFAULT 0,
        completed_at datetime NULL,
        PRIMARY KEY (id),
        KEY student_id (student_id),
        KEY course_id (course_id),
        UNIQUE KEY student_course (student_id, course_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Create progress table
    $table_name = $wpdb->prefix . 'lms_progress';
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        student_id bigint(20) UNSIGNED NOT NULL,
        lesson_id bigint(20) UNSIGNED NOT NULL,
        completed tinyint(1) DEFAULT 0,
        completed_at datetime NULL,
        time_spent int DEFAULT 0,
        PRIMARY KEY (id),
        KEY student_id (student_id),
        KEY lesson_id (lesson_id),
        UNIQUE KEY student_lesson (student_id, lesson_id)
    ) $charset_collate;";
    
    dbDelta($sql);
});
?>
