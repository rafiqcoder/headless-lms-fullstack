<?php
/**
 * WordPress CRON Management.
 *
 * @package WordPress
 */

ignore_user_abort( true );

if ( ! headers_sent() ) {
	header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
	header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
}

if ( ! defined( 'ABSPATH' ) ) {
	/** Set up WordPress environment */
	require_once __DIR__ . '/wp-load.php';
}

if ( ! ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
	header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
	header( 'X-Robots-Tag: noindex, nofollow' );
	die( '0' );
}

if ( ! defined( 'WP_CRON_LOCK_TIMEOUT' ) ) {
	define( 'WP_CRON_LOCK_TIMEOUT', 60 );
}

if ( function_exists( 'fastcgi_finish_request' ) && ini_get( 'cgi.fix_pathinfo' ) ) {
	fastcgi_finish_request();
}

wp_cron();
