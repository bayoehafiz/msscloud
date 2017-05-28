<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

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
define('DB_NAME', 'msscloud.id');

/** MySQL database username */
define('DB_USER', 'mssdb');

/** MySQL database password */
define('DB_PASSWORD', 'QFY48wbzR@hu-7GZ');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '.Ec6G?aQYA3/e#)KWQRj|!Bb0[zz<|d>QdbQ-mDcb>}:aQ1*}Z/Fw%^ljs7$F#5X');
define('SECURE_AUTH_KEY',  '5K^+4CEHcg7i[5-4@ZuT((J)Fx4p=Djr3X~qPfvk<;E<|,6>|Sx )Efnog-7Kk4+');
define('LOGGED_IN_KEY',    'D?fe8Nz9qh`IJ3xtX.gyHCAwv27WbO_e3AhZj|^CC&7KpXH[8IJg^TtGDf>z7r[[');
define('NONCE_KEY',        'LAYS?=FRrJ8NkAqAstD0Q^)9(Ec`%MQ4|H0I Si@bi~wARz:bA+p:3zpB*4+5q)M');
define('AUTH_SALT',        'Xc58 g!lHvLs~.ASrx0JS-sMLJ1).$[D.v0#(/_jcORZvzc77ch&j8j/0`qjMv0[');
define('SECURE_AUTH_SALT', ',krMYe:Bb.(uiam,k[n@*=d7Y@G:t0R[K.bc2hQm,C5YqK~c3z3L)F!kTC<Z[V#o');
define('LOGGED_IN_SALT',   '$>GHN4%_+!i:8vq~$C?ajj1}v!Z +KY+;u=JT%8o:FD.6c{K/;`Yy7l/DEV}.Ih7');
define('NONCE_SALT',       'k%7$a+7isV+anjBuv|gnv2,}q.J(22;8~% ZN O1Qb&s 5X?jZrb[ax{Ff={))cg');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'i0t7ygcn4f_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define( 'WP_AUTO_UPDATE_CORE', true );
// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

define('WP_ALLOW_REPAIR', true);
