<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK SETTINGS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$settings      = array(
	'menu_title' => 'Theme Options',
	'menu_type'  => 'add_menu_page',
	'menu_slug'  => 'cs-framework',
	'ajax_save'  => false,
);
$theme_name	= 'NRGhost';
$docs_url	= 'http://demo.nrgthemes.com/projects/nrghostwp/documentation/';



// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options	= array();



// ----------------------------------------
// general options section                -
// ----------------------------------------
$options[]	= array(
	'name'		=> 'general',
	'title'		=> 'General',
	'icon'		=> 'fa fa-globe',
	'fields'	=> array(

		array(
			'id'		=> 'show-preloader',
			'type'		=> 'switcher',
			'title'		=> 'Show preloader',
			'label'		=> 'Show animation while site is loading',
			'default'	=> true,
		),

		array(
			'id'		=> 'color-scheme',
			'type'		=> 'image_select',
			'title'		=> 'Website color scheme',
			'radio'		=> true,
			'options'	=> array(
				'colour-0'	=> T_URI . '/assets/img/colors/orange.png',		// orange
				'colour-1'	=> T_URI . '/assets/img/colors/blue.png',		// blue
				'colour-2'	=> T_URI . '/assets/img/colors/green.png',		// green
				'colour-3'	=> T_URI . '/assets/img/colors/violet.png',		// violet
			),
			'default'	=> 'colour-0',
		),

		array(
			'id'			=> 'tracking-code',
			'type'			=> 'textarea',
			'title'			=> 'Tracking Code',
			'sanitize'		=> false,
			'attributes'	=> array(
				'rows'			=> 2,
				'placeholder'	=> "Put your tracking code here...",
			),
		),

	),
);



// ----------------------------------------
// header options section                 -
// ----------------------------------------
$glyph_link = 'http://www.w3schools.com/bootstrap/bootstrap_ref_comp_glyphs.asp';
$options[]	= array(
	'name'		=> 'header',
	'title'		=> 'Header',
	'icon'		=> 'fa fa-arrow-up',
	'fields' => array(

		array(
			'id'		=> 'site-favicon',
			'type'		=> 'upload',
			'title'		=> 'Favicon',
			'default'	=> get_template_directory_uri() . '/assets/img/favicon.ico',
		),

		array(
			'id'		=> 'site-logo',
			'type'		=> 'upload',
			'title'		=> 'Site logo',
			'default'	=> get_template_directory_uri() . '/assets/img/logo.png',
		),

		array(
			'id'		=> 'enable-header-adv',
			'type'		=> 'switcher',
			'title'		=> 'Enable header advertising block (can be changed for every page individually)',
			'default'	=> false,
		),

		array(
			'id'		=> 'header-adv',
			'type'		=> 'textarea',
			'title'		=> 'Header Advertising Block',
			'sanitize'	=> false,
		),

		array(
			'id'		=> 'enable-header-add-menu',
			'type'		=> 'switcher',
			'title'		=> 'Enable header additional menu',
			'default'	=> true,
		),

		array(
			'id'			=> 'enable-add-menu-cart',
			'type'			=> 'switcher',
			'title'			=> 'Show cart in additional menu',
			'default'		=> true,
			'help'			=> 'Cart icon will be displayed only when WooCommerce plugin is active',
			'dependency'	=> array( 'enable-header-add-menu', '==', 'true' ),
		),

		array(
			'id'				=> 'header-add-menu',
			'type'				=> 'group',
			'title'				=> 'Header additional menu',
			'button_title'		=> 'Add New',
			'accordion_title'	=> 'Menu item',
			'dependency'		=> array( 'enable-header-add-menu', '==', 'true' ),
			'fields'			=> array(

				array(
					'id'			=> 'title',
					'type'			=> 'text',
					'title'			=> 'Title',
					'default'		=> 'Title',
				),

				array(
					'id'			=> 'icon',
					'type'			=> 'text',
					'title'			=> 'Icon',
					'after'   		=> '<p class="cs-text-muted">Class of icon. For example, "<strong>glyphicon glyphicon-time</strong>". <br>You can find list of icons here: <a href="' . $glyph_link . '" target="_blank">Glyphicons</a></p>',
				),

				array(
					'id'			=> 'enable-link',
					'type'			=> 'switcher',
					'title'			=> 'Enable link',
					'default'		=> false,
				),

				array(
					'id'			=> 'link',
					'type'			=> 'text',
					'title'			=> 'Link',
					'dependency'	=> array( 'enable-link', '==', 'true' ),
					'default'		=> '#',
				),
			),
			'default'			=> array(
				array(
					'icon'			=> 'glyphicon glyphicon-time',
					'title'			=> '<b>24/7</b> Customer Support',
					'enable-link'	=> false,
					'link'			=> '',
				),
			),
		),
	),
);



// ----------------------------------------
// footer options section                 -
// ----------------------------------------
$options[]	= array(
	'name'		=> 'footer',
	'title'		=> 'Footer',
	'icon'		=> 'fa fa-arrow-down',
	'fields'	=> array(

		array(
			'id'		=> 'enable-footer-sidebar',
			'type'		=> 'switcher',
			'title'		=> 'Enable footer sidebar',
			'default'	=> true,
		),

		array(
			'id'		=> 'enable-footer-socials',
			'type'		=> 'switcher',
			'title'		=> 'Enable footer socials',
			'default'	=> true,
		),

		array(
			'id'		=> 'enable-footer-menu',
			'type'		=> 'switcher',
			'title'		=> 'Enable footer menu',
			'default'	=> true,
		),

		array(
			'id'			=> 'footer-copy-text',
			'type'			=> 'textarea',
			'title'			=> 'Footer Copyright Text',
			'default'		=> '&copy; 2015 All rights reserved. NRGHost'
		),

		array(
			'id'		=> 'enable-footer-line-menu',
			'type'		=> 'switcher',
			'title'		=> 'Enable footer bottom line',
			'default'	=> true,
		),

		array(
			'id'				=> 'footer-line',
			'type'				=> 'group',
			'title'				=> 'Footer bottom line',
			'button_title'		=> 'Add New',
			'accordion_title'	=> 'Menu item',
			'dependency'		=> array( 'enable-footer-line-menu', '==', 'true' ),
			'fields'			=> array(

				array(
					'id'			=> 'title',
					'type'			=> 'text',
					'title'			=> 'Title',
					'default'		=> 'Title',
				),

				array(
					'id'			=> 'image',
					'type'			=> 'upload',
					'title'			=> 'Image',
				),

				array(
					'id'			=> 'enable-link',
					'type'			=> 'switcher',
					'title'			=> 'Enable link',
					'default'		=> false,
				),

				array(
					'id'			=> 'link',
					'type'			=> 'text',
					'title'			=> 'Link',
					'dependency'	=> array( 'enable-link', '==', 'true' ),
					'default'		=> '#',
				),
			),
			'default'			=> array(
				array(
					'image'			=> get_template_directory_uri() . '/assets/img/icon-22.png',
					'title'			=> '24/7 Customer Support',
					'enable-link'	=> false,
					'link'			=> '',
				),
				array(
					'image'			=> get_template_directory_uri() . '/assets/img/icon-23.png',
					'title'			=> 'support@nrghost.com',
					'enable-link'	=> true,
					'link'			=> 'mailto:support@nrghost.com',
				),
				array(
					'image'			=> get_template_directory_uri() . '/assets/img/icon-24.png',
					'title'			=> '+48 555 8753 005',
					'enable-link'	=> true,
					'link'			=> 'tel:485558753005',
				),
				array(
					'image'			=> get_template_directory_uri() . '/assets/img/icon-25.png',
					'title'			=> 'Live Chat',
					'enable-link'	=> true,
					'link'			=> '#',
				),
			),
		),
	),
);



// ----------------------------------------
// Single post section                    -
// ----------------------------------------
$options[]	= array(
	'name'		=> 'single',
	'title'		=> 'Single',
	'icon'		=> 'fa fa-file-text-o',
	'fields'	=> array(

		array(
			'id'		=> 'enable-related-posts',
			'type'		=> 'switcher',
			'title'		=> 'Show related posts on single',
			'default'	=> true,
		),

	),
);



// ----------------------------------------
// Register/Login options section         -
// ----------------------------------------
$options[]   = array(
	'name'		=> 'register',
	'title'		=> 'Register / Login',
	'icon'		=> 'fa fa-key',
	'fields'	=> array(

		array(
			'type'    => 'notice',
			'class'   => 'info',
			'content' => '<strong>Be careful</strong>: You have to choose page which based on template "Log in".',
		),

		array(
			'id'		=> 'login-page',
			'type'		=> 'select',
			'title'		=> 'Select Login/Register page',
			'options'	=> 'pages',
			'query_args'	=> array(
				'meta_key' => '_wp_page_template',
				'meta_value' => 'sign-in.php'
			),
		),

		array(
			'id'		=> 'enable-header-login-buttons',
			'type'		=> 'switcher',
			'title'		=> 'Show login / register buttons in header',
			'default'	=> true,
		),

		array(
			'id'			=> 'login-form-title',
			'type'			=> 'text',
			'title'			=> 'Login form title',
			'default'		=> 'Login Form',
		),

		array(
			'id'			=> 'login-form-description',
			'type'			=> 'textarea',
			'title'			=> 'Login form description',
			'attributes'	=> array(
				'rows'			=> 3,
			),
			'default'		=> 'Curabitur nunc neque, mollis viverra ex in, auctor elementum mi. Donec non purus felis. Duis interdum mi id purub',
		),

		array(
			'id'			=> 'register-form-title',
			'type'			=> 'text',
			'title'			=> 'Register form title',
			'default'		=> 'Registration Form',
		),

		array(
			'id'			=> 'register-form-description',
			'type'			=> 'textarea',
			'title'			=> 'Register form description',
			'attributes'	=> array(
				'rows'			=> 3,
			),
			'default'		=> 'Curabitur nunc neque, mollis viverra ex in, auctor elementum mi. Donec non purus felis. Duis interdum mi id purub',
		),

		array(
			'id'			=> 'recovery-form-title',
			'type'			=> 'text',
			'title'			=> 'Recovery form title',
			'default'		=> 'Recovery password',
		),

		array(
			'id'			=> 'recovery-form-description',
			'type'			=> 'textarea',
			'title'			=> 'Recovery form description',
			'attributes'	=> array(
				'rows'			=> 3,
			),
			'default'		=> 'Please enter your <b>username</b> or <b>email address</b>.<br> You will receive a link to create a new password via email.',
		),

	),
);



// ----------------------------------------
// socials options section                -
// ----------------------------------------
$options[]	= array(
	'name'		=> 'socials',
	'title'		=> 'Socials',
	'icon'		=> 'fa fa-group',
	'fields'	=> array(

		array(
			'id'				=> 'socials',
			'type'				=> 'group',
			'title'				=> 'Socials',
			'button_title'		=> 'Add Social',
			'accordion_title'	=> 'New Social',
			'fields'			=> array(

				array(
					'id'			=> 'title',
					'type'			=> 'text',
					'title'			=> 'Title',
					'default'		=> 'Name',
				),

				array(
					'id'			=> 'link',
					'type'			=> 'text',
					'title'			=> 'Link',
					'default'		=> '#',
				),

				array(
					'id'			=> 'icon',
					'type'			=> 'icon',
					'title'			=> 'Icon',
					'default'		=> 'fa fa-wordpress',
				),

				array(
					'id'			=> 'color',
					'type'			=> 'color_picker',
					'title'			=> 'Background Color',
					'default'		=> '#007bb5',
				),

			),
			'default' => array(
				array(
					'title'		=> 'Facebook',
					'link'		=> 'http://facebook.com/',
					'icon'		=> 'fa fa-facebook',
					'color'		=> '#3b5998',
				),
				array(
					'title'		=> 'Google Plus',
					'link'		=> 'http://plus.google.com/',
					'icon'		=> 'fa fa-google-plus',
					'color'		=> '#e02f2f',
				),
				array(
					'title'		=> 'Twitter',
					'link'		=> 'http://twitter.com/',
					'icon'		=> 'fa fa-twitter',
					'color'		=> '#55acee',
				),
				array(
					'title'		=> 'LinkedIn',
					'link'		=> 'http://linkedin.com/',
					'icon'		=> 'fa fa-linkedin',
					'color'		=> '#007bb5',
				),
			)
		),
	),
);



// ----------------------------------------
// Custom code options section            -
// ----------------------------------------
$options[]	= array(
	'name'		=> 'custom_code',
	'title'		=> 'Custom code',
	'icon'		=> 'fa fa-code',
	'fields'	=> array(

		array(
			'id'			=> 'custom-css',
			'type'			=> 'textarea',
			'title'			=> 'Custom CSS',
			'attributes'	=> array(
				'rows'			=> 10,
				'placeholder'	=> "#sample{\n\tmargin: 0 auto;\n}",
			),
		),

		array(
			'id'			=> 'custom-js',
			'type'			=> 'textarea',
			'title'			=> 'Custom JS',
			'attributes'	=> array(
				'rows'			=> 10,
				'placeholder'	=> "put here some JS code...",
			),
		),
	),
);



// ------------------------------
// a separator                  -
// ------------------------------
$options[]	= array(
	'name'		=> 'separator_1',
	'title'		=> '',
	'icon'		=> ''
);



// ----------------------------------------
// documentation section                 -
// ----------------------------------------
$options[]	= array(
	'name'		=> 'documentation',
	'title'		=> 'Documentation',
	'icon'		=> 'fa fa-info-circle',

	// begin: fields
	'fields'	=> array(

		array(
			'type'		=> 'content',
			'content'	=> '<h4>All information about ' . $theme_name . ' theme and some how\'s-to you can find on this <a href="' . $docs_url . '" target="_blank">info page</a>.</h4>',
		),

	), // end: fields
);



// -----------------------------
// backup option               -
// -----------------------------
$options[]	= array(
	'name'		=> 'backup_option',
	'title'		=> 'Backup',
	'icon'		=> 'fa fa-check',
	'fields'	=> array(

		array(
			'type'	=> 'backup',
		),

	),
); // end: backup option



// ------------------------------
// a separator                  -
// ------------------------------
$options[]	= array(
	'name'		=> 'separator_2',
	'title'		=> '',
	'icon'		=> ''
);



CSFramework::instance( $settings, $options );