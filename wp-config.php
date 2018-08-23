<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'shayari');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'rO` yk234VY6_Bjg)|Qe#g6^TUw6<BUWL]W)fU*%weiHogqZdYkK.FOqS(q6nyeP');
define('SECURE_AUTH_KEY',  '`%cW%)xr8l(,TN+%-PDaQdJcB2d;]f7A`.iSm:}L<<Bmg&CIhDZ|{*@MrL:vM;:@');
define('LOGGED_IN_KEY',    'gH$vA<?DZ1?oY_8TR$Y?,o6DboqJuZxlAr`vt)2Eaq)K%EZoo_BNkYeuv$<#cOdl');
define('NONCE_KEY',        '>?ueM^L&6Rny,IP,2($+2Wh6F!m5m+zObYN lA)[8rp-I?A!!G &o_y{0gHl822Z');
define('AUTH_SALT',        'Yj13<5d,55FUUo!==#;<UiJUOz,?c<YfUVLg<E_/T]B`3EqZR*KsP<fuOIn/hZsO');
define('SECURE_AUTH_SALT', '7+]+0BROU9^{v-H4lf7=miG9ML^w$X9|-!q|+j+h{=:Eb6@Xczd/8!?R~G+HB%VN');
define('LOGGED_IN_SALT',   'QKBMgkMcNSBt%h*zdFnC:@dY:(?9]hwC=/WmZ[xf<yuy=sK^wL^|h]hvb)5=,xbG');
define('NONCE_SALT',       '$hM}`-2aTt6:8AI?H]WR5rKUTQ ctja7J(op$m`/b,w7d(EX9OSPsR<<*.%[wL!`');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost');
define('PATH_CURRENT_SITE', '/wordpress/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);


/* That's all, stop editing! Happy blogging. */define('WP_ALLOW_MULTISITE', true);


/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
