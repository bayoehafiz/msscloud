<?php
/**
 * Loads needed stuffs.
 *
 * @package nrghost
 * @since 1.0.0
 */



defined( 'F_PATH' )		OR	define(	'F_PATH',	'core' );
defined( 'T_NAME' )		OR	define(	'T_NAME',	'nrghost');
defined( 'F_DIR' )		OR	define(	'F_DIR',	F_PATH );
defined( 'T_URI' )		OR	define(	'T_URI', 	get_template_directory_uri() );
defined( 'T_PATH' )		OR	define(	'T_PATH',	get_template_directory() );


// ----------------------------------------------------------------------------------------------------
// Header Walker Class integration
// ----------------------------------------------------------------------------------------------------
require_once	get_template_directory() . '/core/classes/headerwalker.class.php';

// ----------------------------------------------------------------------------------------------------
// Footer Walker Class integration
// ----------------------------------------------------------------------------------------------------
require_once	get_template_directory() . '/core/classes/footerwalker.class.php';

// ----------------------------------------------------------------------------------------------------
// NrghostWalkerComment Class include
// ----------------------------------------------------------------------------------------------------
require_once	get_template_directory() . '/core/classes/commentswalker.class.php';

// ----------------------------------------------------------------------------------------------------
// Aqua Resizer Class integration
// ----------------------------------------------------------------------------------------------------
require_once	get_template_directory() . '/core/classes/aq_resizer.class.php';

// ----------------------------------------------------------------------------------------------------
// TGM Activation Plugin integration
// ----------------------------------------------------------------------------------------------------
require_once	get_template_directory() . '/core/classes/tgm-activation.class.php';