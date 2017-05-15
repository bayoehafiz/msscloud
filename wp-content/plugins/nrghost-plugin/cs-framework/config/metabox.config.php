<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// METABOX OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();



// -----------------------------------------
// Page Side Metabox Options               -
// -----------------------------------------
$options[]	= array(
	'id'		=> 'nrghost_custom_page_side_options',
	'title'		=> 'Page Options',
	'post_type'	=> 'page',
	'context'	=> 'side',
	'priority'	=> 'default',
	'sections'	=> array(

		array(
			'name'		=> 'section_page',
			'fields'	=> array(

				array(
					'id'			=> 'enable-header-adv',
					'title'			=> 'Show advertise before header',
					'type'			=> 'switcher',
					'default'		=> false,
				),

			),
		),
	),
);



// -----------------------------------------
// Post Side Metabox Options               -
// -----------------------------------------
/*$options[]    = array(
	'id'		=> 'nrghost_custom_post_side_options',
	'title'		=> 'Post Style Side Options',
	'post_type'	=> 'post',
	'context'	=> 'side',
	'priority'	=> 'default',
	'sections'	=> array(

		array(
			'name'		=> 'options',
			'fields'	=> array(

				array(
					'id'			=> 'post-side-swither',
					'title'			=> 'Switcher',
					'type'			=> 'switcher',
					'default'		=> false,
				),

			),
		),
	),
);*/

CSFramework_Metabox::instance( $options );