
<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'embdesign_local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '{r@b;jsK-arA2c5P%Ei%u5yAH0M(&U+Ud?R$>*;MGBIN:5X3r Qv&pv=E%oaU6BE' );
define( 'SECURE_AUTH_KEY',   'q*9cm) *<(rg@dQJ.:scMtU2mIq$9dc;snoQ)kQ{@}XD@zxy+Nm*{41bLiU[e>|H' );
define( 'LOGGED_IN_KEY',     '8}=]z[1$]Q!iR]?d=0qObyX>YsXU @>T[#.A_IZkJOc$L{]~6UV2/{vUS2[C=;@|' );
define( 'NONCE_KEY',         'pZLoB&a1VS:&xmVAS{B}=x.Q<^?}Yp^{[ARQvA;Lh8)H($ot9f(s;VkN}}z{|fVu' );
define( 'AUTH_SALT',         '>YwAA-7)O=N+`tKw3V]990tEK,DPaR(V|>W<aML6d7r()Xp.XE(Xq_5VKB?<!?Jx' );
define( 'SECURE_AUTH_SALT',  '<`bhEF<9VlFS/.IP9lSC}zF.;|oj2,C~G^7@#jN_Ef^`vC6pTia:Sy{g`Y@csQWC' );
define( 'LOGGED_IN_SALT',    'I~g)i2!9xWHDqxTe1WbIPo{snPTce$.eg[V,N6hfY%XJt;=@S/?<Mjg,as/A8iCv' );
define( 'NONCE_SALT',        'O13fA@^X({M+w4C /7?`/Y<8M>a~8Uc`~]2.[of6Wk?A?)xnh9<9:Sh`t@J+8tAh' );
define( 'WP_CACHE_KEY_SALT', 'n~7Dv<e4MVik.!DUWJ)H};ajW|_/G??9Rx(1[O3Bk4zlLSAF^_._<R3Z?,0&}nb`' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}
define('SCRIPT_DEBUG', true);
define('UAG_KEEP_FILES', true);

// Proper debug log path
@ini_set('log_errors', 1);
@ini_set('error_log', ABSPATH . 'wp-content/debug.log');


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
