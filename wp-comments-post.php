<?php
/**
 * Handles Comment Post to WordPress and prevents duplicate comment posting.
 *
 * @package WordPress
 */

if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
		$protocol = 'HTTP/1.0';
	}

	header( 'Allow: POST' );
	header( "$protocol 405 Method Not Allowed" );
	header( 'Content-Type: text/plain' );
	exit;
}

/** Sets up the WordPress Environment. */
require __DIR__ . '/wp-load.php';

nocache_headers();

$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
if ( is_wp_error( $comment ) ) {
	$data = (int) $comment->get_error_data();
	if ( ! empty( $data ) ) {
		wp_die(
			'<p>' . $comment->get_error_message() . '</p>',
			__( 'Comment Submission Failure' ),
			array(
				'response'  => $data,
				'back_link' => true,
			)
		);
	} else {
		exit;
	}
}

$user            = wp_get_current_user();
$cookies_consent = ( isset( $_POST['wp-comment-cookies-consent'] ) );

/**
 * Fires after a comment is retrieved.
 *
 * @since 1.5.0
 *
 * @param array $comment Comment array.
 */
do_action( 'comment_post', $comment->comment_ID, $comment->comment_approved, $cookies_consent );

if ( ! $cookies_consent && ! $user->exists() && ! empty( $_POST['author'] ) && ! empty( $_POST['email'] ) ) {
	setcookie( 'comment_author_' . COOKIEHASH, $_POST['author'], time() + 30000000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
	setcookie( 'comment_author_email_' . COOKIEHASH, $_POST['email'], time() + 30000000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
	if ( ! empty( $_POST['url'] ) ) {
		setcookie( 'comment_author_url_' . COOKIEHASH, esc_url( $_POST['url'] ), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
	}
}

$location = empty( $_POST['redirect_to'] ) ? get_comment_link( $comment ) : $_POST['redirect_to'] . '#comment-' . $comment->comment_ID;

// If user didn't consent to cookies, add specific fragment to identify cookie-less comment.
if ( ! $cookies_consent && ! $user->exists() ) {
	$location = add_query_arg( 'unapproved', $comment->comment_ID, $location );
	$location = add_query_arg( 'moderation-hash', wp_hash( $comment->comment_date_gmt ), $location );
}

/**
 * Filters the location URI to send the commenter after posting.
 *
 * @since 2.0.5
 *
 * @param string $location The 'redirect_to' URI sent via $_POST.
 * @param WP_Comment $comment Comment object.
 */
$location = apply_filters( 'comment_post_redirect', $location, $comment );

wp_safe_redirect( $location, 303 );
