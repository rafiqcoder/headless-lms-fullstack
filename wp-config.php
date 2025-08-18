<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'lms' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'D1<@rbbr{@2ELa;rVF<e&lahFN#( <hUI+3)_PVgs0D%_DkMzFC=KX$2L+338BH:' );
define( 'SECURE_AUTH_KEY',  'xFtM*`#?I|k;8S{dlhzHSR%lb>AmHp-74*s%>gC>t-oejgJYZm#CkO19ihf+hB)z' );
define( 'LOGGED_IN_KEY',    'L~>9_(o]K,U/6,uX%ueA2|ZgNQ91m<xy]?48C7K[3ZP6ouzG_]fyNz_{XAz/#a1%' );
define( 'NONCE_KEY',        'Bk}UnKyn$lt^{d!^5+deSk<k91P.[Bb%IyZP_9PV5f$SfLg+Kkb[FpTH>saYh)^5' );
define( 'AUTH_SALT',        'OS-uX:(t BN}Keio__2^G/w/1TEHb{3lCW8@!MDom*L>KhnLy`NuNT*!If[#gf%w' );
define( 'SECURE_AUTH_SALT', 'UNJL+h`Cm;/hQRa-2Ad#.Fb*kK+&s$H99>>>/ILvx.`kzH/$p>3H^^1hWiIxAbOl' );
define( 'LOGGED_IN_SALT',   '`eO#i7~Mw4K0V=8^wbT&M$qn`WgfE36T]N1x]KD<M,x+vyj *L=7j.x.5/A+Pc~M' );
define( 'NONCE_SALT',       '`;^fe>+o@nY qK2S6aCl]~#d7LZm&W@g1)Zb;h&.LfQ7|j;V4(Su0V!-r$EI`T7,' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

/* Add any custom values between this line and the "stop editing" line. */

// Headless LMS Optimizations
define( 'WP_POST_REVISIONS', 3 ); // Limit post revisions
define( 'AUTOSAVE_INTERVAL', 300 ); // Autosave every 5 minutes
define( 'WP_CRON_LOCK_TIMEOUT', 60 ); // Reduce cron lock timeout

// Security enhancements for headless setup
define( 'DISALLOW_FILE_EDIT', true ); // Disable file editing in admin
define( 'FORCE_SSL_ADMIN', false ); // Set to true if using HTTPS

// Performance optimizations
define( 'WP_MEMORY_LIMIT', '256M' ); // Increase memory limit
define( 'WP_MAX_MEMORY_LIMIT', '512M' ); // Max memory for admin

// API optimizations
define( 'REST_REQUEST_PARAMETER_MAX_SIZE', 1000 ); // Increase REST API parameter size



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
